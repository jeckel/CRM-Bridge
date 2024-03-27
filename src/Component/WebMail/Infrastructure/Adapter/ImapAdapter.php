<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 12:20
 */
declare(strict_types=1);

namespace App\Component\WebMail\Infrastructure\Adapter;

use App\Component\WebMail\Application\Dto\ImapMailDto;
use App\Component\WebMail\Application\Dto\ImapMailHeaderDto;
use App\Component\WebMail\Application\Dto\MailboxStatusDto;
use App\Component\WebMail\Application\Port\ImapPort;
use App\Component\WebMail\Domain\Entity\ImapAccount;
use LogicException;
use PhpImap\Mailbox;

readonly class ImapAdapter implements ImapPort
{
    public function __construct(private ImapMailboxProvider $mailboxProvider) {}

    #[\Override]
    public function listMailboxes(ImapAccount $account): array
    {
        $mailbox = $this->mailboxProvider->getMailbox(
            imapPath: sprintf('{%s:993/imap/ssl}INBOX', $account->uri()),
            login: $account->login(),
            password: $account->password()
        );

        return $mailbox->getListingFolders();
    }

    #[\Override]
    public function getStatus(ImapAccount $account, string $imapPath): MailboxStatusDto
    {
        $mailbox = $this->mailboxProvider->getMailbox($imapPath, $account->login(), $account->password());
        $uids = $mailbox->searchMailbox();
        $minUid = count($uids) > 0 ? $uids[0] : 0;

        /** @phpstan-ignore-next-line  */
        return new MailboxStatusDto($imapPath, $minUid, ...get_object_vars($mailbox->statusMailbox()));
    }

    #[\Override]
    public function getMail(ImapAccount $account, string $imapPath, int $uid): ?ImapMailDto
    {
        $mailbox = $this->mailboxProvider->getMailbox($imapPath, $account->login(), $account->password());
        try {
            $mail = $mailbox->getMail($uid, markAsSeen: false);
        } catch (\Exception $e) {
            if ($e->getMessage() === 'Warning: imap_fetchheader(): UID does not exist') {
                // Mail deleted
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
            imapPath: $imapPath,
            textHtml: $this->fixEncoding($mail->textHtml),
            textPlain: $this->fixEncoding($mail->textPlain),
            uid: $uid,
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
