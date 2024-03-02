<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 13/02/2024 19:00
 */
declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\Service;

use App\Infrastructure\Doctrine\Entity\ImapFolder;
use App\Infrastructure\Doctrine\Repository\ImapFolderRepository;
use App\Infrastructure\Imap\ImapMailbox;
use App\Infrastructure\Imap\Mail\MailProvider;
use JeckelLab\Contract\Infrastructure\System\Clock;

readonly class MailFolderSynchroniser
{
    public function __construct(
        private MailProvider $mailProvider,
        private ImapFolderRepository $folderRepo,
        private Clock $clock
    ) {}

    public function syncFolderEntity(ImapFolder $folder): void
    {
        $account = $folder->getImapAccount();
        $mailbox = ImapMailbox::fromImapAccount($account);

        $searchCriteria = 'ALL';
        //        if (null !== $folder->getLastSyncUid()) {
        //            $searchCriteria = sprintf('SEARCH UID %d:*', $folder->getLastSyncUid());
        //        }
        $mailIds = $mailbox->searchFolder($folder->getName(), $searchCriteria);
        sort($mailIds);

        $count = 0;
        foreach($mailIds as $mailId) {
            $this->mailProvider->getMail($account, $folder->getName(), $mailId)->sync();
            $folder->setLastSyncUid($mailId);
            $count++;
            if ($count % 20 === 0) {
                $folder->setLastSyncDate($this->clock->now());
                $this->folderRepo->persist($folder);
            }
        }

        $folder->setLastSyncDate($this->clock->now());
        $this->folderRepo->persist($folder);
    }
}
