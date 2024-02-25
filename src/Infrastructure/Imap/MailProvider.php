<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 25/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Imap;

use App\Infrastructure\Doctrine\Entity\ImapConfig;
use DateTimeImmutable;
use LogicException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class MailProvider
{
    public function __construct(
        private CacheInterface $imapMailCache
    ) {}

    public function getMail(int $mailId, string $folder, ImapConfig $imapConfig): array
    {
        $key = sprintf('%s-%s-%d', $imapConfig->getId(), $folder, $mailId);
        return $this->imapMailCache->get(
            $key,
            fn(ItemInterface $item) => $this->getMailFromImapConfig($imapConfig, $folder, $mailId, $item)
        );
    }

    private function getMailFromImapConfig(
        ImapConfig $imapConfig,
        string $folder,
        int $mailId,
        ItemInterface $item
    ): array {
        $item->expiresAfter(300);
        $mailbox = ImapMailbox::fromImapConfig($imapConfig);
        $mail = $mailbox->getMail($mailId, $folder);
        return [
            'fromName' => $mail->fromName,
            'fromAddress' => $mail->fromAddress,
            'subject' => $mail->subject,
            'date' => new DateTimeImmutable($mail->date ?? throw new LogicException('Date can not be null')),
        ];
    }
}
