<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\Action;

use App\Component\ContactManagment\Application\Command\AddEmailAddress;
use App\Component\Shared\Identity\ContactId;
use App\Component\Shared\ValueObject\Email;
use App\Infrastructure\Doctrine\Entity\Contact;
use App\Infrastructure\Doctrine\Repository\MailRepository;
use App\Presentation\Controller\Admin\MailCrudController;
use App\Presentation\Form\SelectContactFormType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class AttachContactFromMailAuthor extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly AdminUrlGenerator $urlGenerator,
        private readonly MailRepository $mailRepository
    ) {}

    #[Route(
        path: "/admin/mail/{mailId}/attach_contact",
        name: "attach_contact_from_mail_author",
        methods: ['GET', 'POST']
    )]
    public function __invoke(string $mailId, Request $request): Response
    {
        $mail = $this->mailRepository->getById($mailId);
        $form = $this->createForm(
            SelectContactFormType::class,
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array{contact: Contact} $formData */
            $formData = $form->getData();
            $contact = $formData['contact'];
            $this->messageBus->dispatch(
                new AddEmailAddress(
                    emailAddress: new Email($mail->getFromAddress()),
                    contactId: ContactId::from((string) $contact->getId())
                )
            );
            $this->addFlash('success', 'mail.alert.attach_contact');
            return $this->redirect(
                $this->urlGenerator->setAction(Action::DETAIL)
                    ->setController(MailCrudController::class)
                    ->setEntityId($mail->getId())
                    ->generateUrl()
            );
        }

        return $this->render(
            'admin/page/form.html.twig',
            [
                'page_title' => 'mail.title.attach_contact',
                'form' => $form->createView()
            ]
        );
    }
}
