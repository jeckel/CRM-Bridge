<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 16:02
 */
declare(strict_types=1);

namespace App\Component\DirectCommunicationHub\Application\Adapter;

use App\Component\DirectCommunicationHub\Application\Helper\MailContextManager;
use App\Component\DirectCommunicationHub\Domain\Model\IncomingMail;
use App\Component\DirectCommunicationHub\Domain\Port\IncomingMailRepository;
use App\Component\Shared\Helper\ContextManager;
use App\Component\Shared\Identity\ImapMailId;
use App\Component\Shared\ValueObject\Email;
use App\Infrastructure\Doctrine\Entity\ImapMessage;
use App\Infrastructure\Doctrine\Repository\ContactRepository;
use App\Infrastructure\Doctrine\Repository\ImapMessageRepository;
use Override;

readonly class IncomingMailRepositoryAdapter implements IncomingMailRepository
{
    public function __construct(
        private ImapMessageRepository $repository,
        private ContactRepository $contactRepository,
        private MailContextManager $mailContext
    ) {}

    #[Override]
    public function save(IncomingMail $incomingMail): void
    {
        $mail = $this->repository->findOneBy([
            'id' => $incomingMail->mailId->id()
        ]) ?? (new ImapMessage())
            ->setId($incomingMail->mailId)
            ->setImapAccount($this->mailContext->getImapConfigReference());
        $mail->setMessageId($incomingMail->messageId)
            ->setDate($incomingMail->date)
            ->setSubject($incomingMail->subject)
            ->setFromName($incomingMail->fromName)
            ->setFromAddress((string) $incomingMail->fromAddress)
            ->setToString($incomingMail->toString)
            ->setImapPath($incomingMail->folder)
            ->setTextPlain($incomingMail->textPlain ?? '')
            ->setTextHtml($incomingMail->textHtml ?? '');
        if (null !== ($authorId = $incomingMail->authorId())) {
            $mail->setContact($this->contactRepository->find((string) $authorId));
        }

        $this->repository->persist($mail);
    }

    #[\Override]
    public function findByAuthorEmail(Email $authorEmail): iterable
    {
        $mails = $this->repository->findBy([
            'fromAddress' => (string) $authorEmail
        ]);
        foreach ($mails as $mail) {
            yield new IncomingMail(
                ImapMailId::from((string) $mail->getId()),
                $mail->getMessageId(),
                $mail->getImapPath(),
                $mail->getDate(),
                $mail->getSubject(),
                $mail->getFromName(),
                new Email($mail->getFromAddress()),
                $mail->getToString() ?? '',
                $mail->getTextPlain(),
                $mail->getTextHtml()
            );
        }
    }
}
