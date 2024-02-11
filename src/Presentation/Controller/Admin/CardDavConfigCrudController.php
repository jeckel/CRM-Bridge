<?php

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\CardDavConfig;
use App\Infrastructure\Doctrine\Entity\User;
use App\Infrastructure\Doctrine\Repository\CardDavAddressBookRepository;
use Doctrine\ORM\EntityNotFoundException;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CardDavConfigCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CardDavConfig::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, static function (Action $action) {
                return $action->setIcon('fa fa-eye')
                    ->setCssClass('btn btn-secondary');
            });
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'config.card_dav.field.name');
        yield UrlField::new('uri', 'config.card_dav.field.uri');
        yield TextField::new('login', 'config.card_dav.field.login');
        yield Field::new('password', 'config.card_dav.field.password')
            ->onlyOnForms()
            ->setFormType(PasswordType::class)
            ->setRequired($pageName === Crud::PAGE_NEW);
        yield ArrayField::new('addressBooks', false)
            ->setTemplatePath('admin/field/card_dav_address_books.html.twig')
            ->onlyOnDetail();
    }

    /**
     * @return CardDavConfig
     */
    #[\Override]
    public function createEntity(string $entityFqcn)
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var CardDavConfig $contact */
        $contact = parent::createEntity($entityFqcn);
        $contact->setId(Uuid::uuid4()->toString())
            ->setAccount($user->getAccount());
        return $contact;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function enableSync(AdminContext $context, CardDavAddressBookRepository $addressBookRepository, AdminUrlGenerator $urlGenerator): Response
    {
        $addressBookId = $context->getRequest()->get('addressBookId');
        if (! is_string($addressBookId)) {
            throw new InvalidArgumentException('Address book id must be a string');
        }
        $addressBookRepository->persist(
            $addressBookRepository->getById($addressBookId)
                ->enableSync()
        );
        return $this->redirect(
            $urlGenerator->setAction(Action::DETAIL)
            ->generateUrl()
        );
    }

    /**
     * @throws EntityNotFoundException
     */
    public function disableSync(AdminContext $context, CardDavAddressBookRepository $addressBookRepository, AdminUrlGenerator $urlGenerator): Response
    {
        $addressBookId = $context->getRequest()->get('addressBookId');
        if (! is_string($addressBookId)) {
            throw new InvalidArgumentException('Address book id must be a string');
        }
        $addressBookRepository->persist(
            $addressBookRepository->getById($addressBookId)
                ->disableSync()
        );
        return $this->redirect(
            $urlGenerator->setAction(Action::DETAIL)
                ->generateUrl()
        );
    }
}
