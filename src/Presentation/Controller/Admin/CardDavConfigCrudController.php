<?php

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\CardDavConfig;
use App\Infrastructure\Doctrine\Repository\CardDavAddressBookRepository;
use App\Presentation\Async\Message\SyncAddressBook;
use Doctrine\ORM\EntityNotFoundException;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CardDavConfigCrudController extends AbstractCrudController
{
    public function __construct()
    {
        $this->enableFilterByAccount()
            ->enableNewGenerateUuid()
            ->enableNewAssignAccount();
    }

    #[\Override]
    public static function getEntityFqcn(): string
    {
        return CardDavConfig::class;
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'config.card_dav.title.index')
            ->setPageTitle(Crud::PAGE_NEW, 'config.card_dav.title.new');
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, static function (Action $action) {
                return $action->setIcon('fa fa-plus')
                    ->setLabel('config.card_dav.action.new')
                    ->setCssClass('btn btn-secondary');
            })
            ->update(Crud::PAGE_INDEX, Action::DETAIL, static function (Action $action) {
                return $action->setIcon('fa fa-eye')
                    ->setCssClass('btn btn-secondary');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, static function (Action $action) {
                return $action->setIcon('fa fa-pencil')
                    ->setCssClass('btn btn-secondary');
            })
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER);
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'config.card_dav.field.name');
        yield UrlField::new('uri', 'config.card_dav.field.uri');
        yield TextField::new('login', 'config.card_dav.field.login');
        yield AssociationField::new('account', 'config.field.account')
            ->setPermission('ROLE_SUPER_ADMIN');
        yield Field::new('password', 'config.card_dav.field.password')
            ->onlyOnForms()
            ->setFormType(PasswordType::class)
            ->setRequired($pageName === Crud::PAGE_NEW);
        yield ArrayField::new('addressBooks', false)
            ->setTemplatePath('admin/field/card_dav_address_books.html.twig')
            ->onlyOnDetail();
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

    public function syncNow(AdminContext $context, AdminUrlGenerator $urlGenerator, MessageBusInterface $messageBus): Response
    {
        $addressBookId = $context->getRequest()->get('addressBookId');
        if (! is_string($addressBookId)) {
            throw new InvalidArgumentException('Address book id must be a string');
        }

        $messageBus->dispatch(new SyncAddressBook($addressBookId));
        return $this->redirect(
            $urlGenerator->setAction(Action::DETAIL)
                ->generateUrl()
        );
    }
}
