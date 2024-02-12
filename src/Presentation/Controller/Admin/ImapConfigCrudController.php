<?php

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\ImapConfig;
use App\Infrastructure\Doctrine\Repository\ImapConfigRepository;
use App\Infrastructure\Imap\ImapMailbox;
use App\Presentation\Form\Imap\SelectFoldersFormType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ImapConfigCrudController extends AbstractCrudController
{
    public function __construct()
    {
        $this->enableFilterByAccount()
            ->enableNewGenerateUuid()
            ->enableNewAssignAccount();
    }

    public static function getEntityFqcn(): string
    {
        return ImapConfig::class;
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'config.imap.title.index')
            ->setPageTitle(Crud::PAGE_NEW, 'config.imap.title.new');
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        $listFolders = Action::new('select_folders', 'config.imap.action.select_folders')
            ->linkToCrudAction('selectFolders');
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, static function (Action $action) {
                return $action->setIcon('fa fa-eye')
                    ->setCssClass('btn btn-secondary');
            })
            ->update(Crud::PAGE_INDEX, Action::NEW, static function (Action $action) {
                return $action->setIcon('fa fa-plus')
                    ->setLabel('config.imap.action.new')
                    ->setCssClass('btn btn-secondary');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, static function (Action $action) {
                return $action->setIcon('fa fa-pencil')
                    ->setCssClass('btn btn-secondary');
            })
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_DETAIL, $listFolders)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'config.imap.field.name');
        yield TextField::new('uri', 'config.imap.field.uri');
        yield TextField::new('login', 'config.imap.field.login');
        yield Field::new('password', 'config.imap.field.password')
            ->onlyOnForms()
            ->setFormType(PasswordType::class)
            ->setRequired($pageName === Crud::PAGE_NEW);
        yield AssociationField::new('account', 'config.field.account')
            ->setPermission('ROLE_SUPER_ADMIN')
            ->hideOnForm();
        yield ArrayField::new('folders', 'config.imap.field.synced_folders');
    }

    public function selectFolders(
        AdminContext $context,
        AdminUrlGenerator $urlGenerator,
        ImapConfigRepository $repository
    ): Response {
        /** @var ImapConfig $imap */
        $imap = $context->getEntity()->getInstance();
        $mailbox = ImapMailbox::fromImapConfig($imap);
        $folders = array_map(
            fn(array $folder) => $folder['shortpath'],
            $mailbox->listFolders()
        );

        $form = $this->createForm(
            type: SelectFoldersFormType::class,
            data: [
                'folders' => $imap->getFolders(),
            ],
            options: [
                'folders' => array_combine($folders, $folders)
            ]
        );

        $form->handleRequest($context->getRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array{folders: array<int, string>} $formData */
            $formData = $form->getData();
            $imap->setFolders($formData['folders']);
            $repository->persist($imap);
            return $this->redirect(
                $urlGenerator->setAction(Action::DETAIL)
                    ->generateUrl()
            );
        }

        return $this->render(
            'admin/page/form.html.twig',
            [
                'page_title' => 'imap.title.setup',
                'form' => $form->createView()
            ]
        );
    }
}
