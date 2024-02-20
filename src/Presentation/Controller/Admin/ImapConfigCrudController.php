<?php

namespace App\Presentation\Controller\Admin;

use App\Component\Shared\Identity\ImapConfigId;
use App\Infrastructure\Doctrine\Entity\ImapConfig;
use App\Infrastructure\Doctrine\Repository\ImapConfigRepository;
use App\Infrastructure\Imap\ImapMailbox;
use App\Presentation\Async\Message\SyncMailbox;
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
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ImapConfigCrudController extends AbstractCrudController
{
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
        $actions = parent::configureActions($actions);
        $listFolders = Action::new('select_folders', 'config.imap.action.select_folders')
            ->linkToCrudAction('selectFolders');
        $syncMail = Action::new('sync_mail', 'config.imap.action.sync_mails')
            ->displayIf(static fn(ImapConfig $config): bool => count($config->getFolders()) > 0)
            ->linkToCrudAction('syncFolders');
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->add(Crud::PAGE_DETAIL, $listFolders)
            ->add(Crud::PAGE_DETAIL, $syncMail);
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
        yield ArrayField::new('folders', 'config.imap.field.synced_folders')
            ->hideOnForm();
    }

    public function selectFolders(
        AdminContext $context,
        AdminUrlGenerator $urlGenerator,
        ImapConfigRepository $repository
    ): Response {
        /** @var ImapConfig $imap */
        $imap = $context->getEntity()->getInstance();
        $mailbox = ImapMailbox::fromImapConfig($imap);
        /** @var string[] $folders */
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

    public function syncFolders(
        AdminContext $context,
        AdminUrlGenerator $urlGenerator,
        MessageBusInterface $messageBus
    ): Response {
        /** @var ImapConfig $imap */
        $imap = $context->getEntity()->getInstance();

        $messageBus->dispatch(new SyncMailbox(ImapConfigId::from($imap->getId())));

        $this->addFlash('success', 'config.imap.alert.sync_mails_requested');
        return $this->redirect(
            $urlGenerator->setAction(Action::DETAIL)
                ->generateUrl()
        );
    }
}
