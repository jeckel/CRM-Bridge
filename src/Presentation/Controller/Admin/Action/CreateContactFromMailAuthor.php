<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\Action;

use App\Infrastructure\Doctrine\Entity\Mail;
use App\Infrastructure\Doctrine\Repository\MailRepository;
use App\Presentation\Async\Message\CreateContact;
use App\Presentation\Controller\Admin\MailCrudController;
use App\Presentation\Form\ContactFormType;
use App\ValueObject\Email;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class CreateContactFromMailAuthor extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly AdminUrlGenerator $urlGenerator,
        private readonly MailRepository $mailRepository
    ) {}

    #[Route(
        path: "/admin/mail/{mailId}/create_contact",
        name: "create_contact_from_mail_author",
        methods: ['GET', 'POST']
    )]
    public function __invoke(string $mailId, Request $request): Response
    {
        /** @var Mail $mail */
        $mail = $this->mailRepository->getById($mailId);
        $form = $this->createForm(
            ContactFormType::class,
            [
                'displayName' => $mail->getFromName(),
                'email' => $mail->getFromAddress()
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array<string, string> $formData */
            $formData = $form->getData();
            $this->messageBus->dispatch(
                new CreateContact(
                    displayName: $formData['displayName'],
                    firstName: $formData['firstName'],
                    lastName: $formData['lastName'],
                    email: new Email($formData['email']),
                    company: $formData['company'],
                )
            );
            $this->addFlash('success', 'mail.alert.contact_adding_to_address_book');
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
                'page_title' => 'mail.title.add_to_address_book',
                'form' => $form->createView()
            ]
        );
    }
}
