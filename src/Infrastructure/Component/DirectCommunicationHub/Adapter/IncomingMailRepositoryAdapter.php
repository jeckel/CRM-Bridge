<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 06/02/2024 16:02
 */
declare(strict_types=1);

namespace App\Infrastructure\Component\DirectCommunicationHub\Adapter;

use App\Component\Shared\Identity\MailId;
use App\Component\Shared\ValueObject\Email;
use App\Domain\Component\DirectCommunicationHub\Model\IncomingMail;
use App\Domain\Component\DirectCommunicationHub\Port\IncomingMailRepository;
use App\Infrastructure\Doctrine\Entity\Mail;
use App\Infrastructure\Doctrine\Repository\ContactRepository;
use App\Infrastructure\Doctrine\Repository\MailRepository;
use Override;

readonly class IncomingMailRepositoryAdapter implements IncomingMailRepository
{
    public function __construct(
        private MailRepository $repository,
        private ContactRepository $contactRepository
    ) {}

    #[Override]
    public function save(IncomingMail $incomingMail): void
    {
        $mail = $this->repository->find($incomingMail->mailId->id()) ?? (new Mail())->setId($incomingMail->mailId->id());
        $mail->setMessageId($incomingMail->messageId)
            ->setDate($incomingMail->date)
            ->setSubject($incomingMail->subject)
            ->setFromName($incomingMail->fromName)
            ->setFromAddress((string) $incomingMail->fromAddress)
            ->setToString($incomingMail->toString)
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
        $mails = $this->repository->findBy(['fromAddress' => (string) $authorEmail]);
        foreach ($mails as $mail) {
            yield new IncomingMail(
                MailId::from($mail->getId()),
                $mail->getMessageId(),
                $mail->getDate(),
                $mail->getSubject(),
                $mail->getFromName(),
                new Email($mail->getFromAddress()),
                $mail->getToString(),
                $mail->getTextPlain(),
                $mail->getTextHtml()
            );
        }
    }
}
