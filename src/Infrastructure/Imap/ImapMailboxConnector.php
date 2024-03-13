<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 12/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Imap;

use App\Infrastructure\Doctrine\Entity\ImapAccount;
use App\Infrastructure\Imap\Exception\MailboxNotFoundException;
use App\Infrastructure\Imap\Mail\ImapMailDto;
use App\Infrastructure\Imap\Mail\ImapMailHeaderDto;
use Exception;
use LogicException;
use PhpImap\IncomingMail;
use PhpImap\Mailbox;

class ImapMailboxConnector
{
    private Mailbox $mailbox;

    public function __construct(
        private readonly string $host,
        private readonly string $login,
        private readonly string $password,
        private string $currentMailbox,
    ) {}

    public function __destruct()
    {
        if (isset($this->mailbox)) {
            $this->mailbox->disconnect();
        }
    }

    public static function fromImapAccount(
        ImapAccount $imapConfig,
        ?string $imapPath = null
    ): ImapMailboxConnector {
        if (null === $imapPath) {
            $imapPath = sprintf('{%s:993/imap/ssl}INBOX', $imapConfig->getUri());
        }
        return new ImapMailboxConnector(
            $imapPath,
            $imapConfig->getLogin(),
            $imapConfig->getPassword(),
            $imapPath,
        );
    }

    public function getMailbox(): Mailbox
    {
        if (! isset($this->mailbox)) {
            $this->mailbox = new Mailbox(
                imapPath: $this->host,
                login: $this->login,
                password: $this->password,
                serverEncoding: 'UTF-8'
            );
            $this->mailbox->setPathDelimiter('/');
            // Ignore attachments for now
            $this->mailbox->setAttachmentsIgnore(true);
        }
        return $this->mailbox;
    }

    /**
     * @return list<string>
     * @throws Exception
     */
    public function listFolders(): array
    {
        return $this->getMailbox()->getListingFolders();
    }

    public function switchMailbox(string $imapPath): self
    {
        if ($this->currentMailbox === $imapPath) {
            return $this;
        }
        try {
            $this->getMailbox()->switchMailbox($imapPath);
            $this->currentMailbox = $imapPath;
        } catch (\Throwable $e) {
            throw new MailboxNotFoundException($imapPath, $e);
        }
        return $this;
    }

    /**
     * @throws MailboxNotFoundException
     */
    public function statusMailbox(?string $imapPath = null): ImapMailboxDto
    {
        if (null !== $imapPath) {
            $this->switchMailbox($imapPath);
        }
        /** @phpstan-ignore-next-line  */
        return new ImapMailboxDto($this->currentMailbox, ...get_object_vars($this->getMailbox()->statusMailbox()));
    }

    /**
     * @return int[]
     */
    public function searchFolder(string $imapPath, string $criteria = 'ALL'): array
    {
        return $this->switchMailbox($imapPath)
            ->getMailbox()
            ->searchMailbox($criteria);
    }

    /**
     * @throws Exception
     */
    public function getMailHeader(int $mailUid, ?string $imapPath = null): ?ImapMailHeaderDto
    {
        if (null !== $imapPath) {
            $this->switchMailbox($imapPath);
        }
        try {
            $headers = $this->getMailbox()->getMailHeader($mailUid);
        } catch (\Exception $e) {
            if ($e->getMessage() === 'Warning: imap_fetchheader(): UID does not exist') {
                return null;
            }
            throw $e;
        }
        /** @phpstan-ignore-next-line  */
        return new ImapMailHeaderDto(...get_object_vars($headers));
    }

    /**
     * @throws Exception
     */
    public function getMail(int $mailUid, ?string $imapPath = null): ?ImapMailDto
    {
        if (null !== $imapPath) {
            $this->switchMailbox($imapPath);
        }
        try {
            $mail = $this->getMailbox()->getMail($mailUid, markAsSeen: false);
        } catch (\Exception $e) {
            if ($e->getMessage() === 'Warning: imap_fetchheader(): UID does not exist') {
                return null;
            }
            throw $e;
        }
        if (null === $mail->headersRaw) {
            throw new LogicException('No headers found');
        }
        $mail->headersRaw = $this->fixEncoding($mail->headersRaw);
        $mail->subject = $this->fixEncoding($mail->subject);
        return new ImapMailDto(
            /** @phpstan-ignore-next-line */
            headers: new ImapMailHeaderDto(...get_object_vars($mail)),
            imapPath: $this->currentMailbox,
            textHtml: $this->fixEncoding($mail->textHtml),
            textPlain: $this->fixEncoding($mail->textPlain),
            uid: $mailUid,
            messageUniqueId: $mail->messageId . '-' . md5($mail->headersRaw),
            hasAttachments: $mail->hasAttachments()
        );
    }

    protected function fixEncoding(?string $text, string $expectedEncoding = 'UTF-8'): string
    {
        if (null === $text) {
            return '';
        }
        $detectedEncoding = mb_detect_encoding($text);
        if (false === $detectedEncoding) {
            $detectedEncoding = null;
        }
        // Note: Converting from UTF-8 to UTF-8 will fix bad encodings in source text
        return mb_convert_encoding($text, $expectedEncoding, $detectedEncoding);
    }
}
