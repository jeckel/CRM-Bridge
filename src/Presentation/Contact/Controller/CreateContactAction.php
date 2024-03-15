<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 14/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Contact\Controller;

use App\Component\ContactManagment\Application\Command\CreateContact;
use App\Component\ContactManagment\Application\Dto\ContactDto;
use App\Component\Shared\ValueObject\Email;
use App\Infrastructure\Doctrine\Entity\CardDavAddressBook;
use App\Infrastructure\Doctrine\Entity\Company;
use App\Presentation\Contact\Form\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @phpstan-type ContactFormDataType array{
 *     companyNew: ?string,
 *     company: ?Company,
 *     displayName: string,
 *     firstName: ?string,
 *     lastName: ?string,
 *     email: string,
 *     addressBook: CardDavAddressBook
 * }
 */
#[Route(
    path: "/contact",
    name: "contact."
)]
class CreateContactAction extends AbstractController
{
    #[Route(
        path: "/contact/new",
        name: "new",
        methods: ['GET', 'POST']
    )]
    public function __invoke(Request $request, MessageBusInterface $messageBus): Response
    {
        $form = $this->createForm(
            type: ContactFormType::class,
            data: [
                'email' => $request->query->getString('email', ''),
                'displayName' => $request->query->getString('displayName', ''),
            ],
            options: [
                'action' => $this->generateUrl('contact.new'),
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @phpstan-var ContactFormDataType $formData */
            $formData = $form->getData();
            $messageBus->dispatch($this->getMessageFromFormData($formData));
            return $this->render('@modal/success.html.twig', [
                'message' => new TranslatableMessage(
                    'contact.flash_message.contact_created',
                    ['%contact%' => $formData['displayName']]
                ),
                'refresh' => [
                    'route' => 'setup.imap.index',
                    'target' => '#imapList'
                ]
            ]);
        }
        return $this->render(
            '@contact/create.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param ContactFormDataType $formData
     * @return CreateContact
     */
    protected function getMessageFromFormData(array $formData): CreateContact
    {
        $company = null;
        if ($formData['companyNew'] !== null) {
            $company = $formData['companyNew'];
        } elseif ($formData['company'] instanceof Company) {
            $company = $formData['company']->getName();
        }
        /** @var CardDavAddressBook $addressBook */
        $addressBook = $formData['addressBook'];

        return new CreateContact(
            contactData: new ContactDto(
                displayName: $formData['displayName'],
                firstName: $formData['firstName'],
                lastName: $formData['lastName'],
                emailAddress: new Email($formData['email']),
                phoneNumber: null,
                company: $company
            ),
            addressBookId: $addressBook->getIdentity()
        );
    }
}
