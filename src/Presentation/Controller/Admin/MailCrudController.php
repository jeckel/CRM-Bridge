<?php

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\ImapMessage;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Override;

class MailCrudController extends AbstractCrudController
{
    #[Override]
    public static function getEntityFqcn(): string
    {
        return ImapMessage::class;
    }

    #[Override]
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('mail.label.singular')
            ->setEntityLabelInPlural('mail.label.plural')
            ->setDefaultSort(['date' => 'DESC'])
        ;
    }

    #[Override]
    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        $createContact = Action::new('create_contact', 'mail.action.create_contact')
            ->linkToRoute(
                routeName: 'create_contact_from_mail_author',
                routeParameters: static fn(ImapMessage $mail): array => ['mailId' => $mail->getId()]
            )
            ->setIcon('fas fa-user-plus')
            ->setCssClass('btn btn-success')
            ->displayIf(static fn(ImapMessage $mail): bool => $mail->getContact() === null)
        ;
        $linkToContact = Action::new('attach_contact', 'mail.action.attach_contact')
            ->linkToRoute(
                routeName: 'attach_contact_from_mail_author',
                routeParameters: static fn(ImapMessage $mail): array => ['mailId' => $mail->getId()]
            )
            ->setIcon('fas fa-user-plus')
            ->setCssClass('btn btn-success')
            ->displayIf(static fn(ImapMessage $mail): bool => $mail->getContact() === null)
        ;
        return $actions
            ->add(Crud::PAGE_DETAIL, $createContact)
            ->add(Crud::PAGE_DETAIL, $linkToContact)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
        ;
    }

    /**
     * @param string $pageName
     * @return iterable<FieldInterface>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Override]
    public function configureFields(string $pageName): iterable
    {
        yield DateTimeField::new('date', label: 'mail.field.date');
        yield TextField::new('subject', label: 'mail.field.subject');
        yield TextField::new('imapConfig', label:'mail.field.imap');
        yield TextField::new('folder', label:'mail.field.folder');
        yield TextField::new('fromName', 'mail.field.from')
            ->setTemplatePath('admin/field/mail_from.html.twig');
        yield TextField::new('toString', 'mail.field.toString')
            ->onlyOnDetail();
        yield TextField::new('textPlain', 'mail.field.message')
            ->onlyOnDetail();
    }
}
