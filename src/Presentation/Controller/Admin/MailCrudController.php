<?php

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\Mail;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MailCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Mail::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('mail.label.singular')
            ->setEntityLabelInPlural('mail.label.plural')
            ->setDefaultSort(['date' => 'DESC'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $sync = Action::new('imap_sync', 'imap.action.sync')
            ->linkToRoute(routeName: 'imap_sync')
            ->setIcon('fa fa-sync')
            ->setCssClass('btn btn-success')
            ->createAsGlobalAction();
        $createContact = Action::new('mail_add_to_address_book', 'mail.action.add_to_address_book')
            ->linkToRoute(
                routeName: 'create_contact_from_mail_author',
                routeParameters: static fn(Mail $mail): array => ['mailId' => $mail->getId()]
            )
            ->setIcon('fas fa-user-plus')
            ->setCssClass('btn btn-success')
            ->displayIf(static fn(Mail $mail): bool => $mail->getContact() === null)
        ;
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $sync)
            ->add(Crud::PAGE_DETAIL, $createContact)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, static function (Action $action) {
                return $action->setIcon('fa fa-eye')
                    ->setCssClass('btn btn-secondary');
            });
    }

    /**
     * @param string $pageName
     * @return iterable<FieldInterface>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function configureFields(string $pageName): iterable
    {
        yield DateTimeField::new('date', label: 'mail.field.date');
        yield TextField::new('subject', label: 'mail.field.subject');
        yield TextField::new('fromName', 'mail.field.from')
            ->setTemplatePath('admin/field/mail_from.html.twig');
        yield TextField::new('toString', 'mail.field.toString')
            ->onlyOnDetail();
        yield TextField::new('textPlain', 'mail.field.message')
            ->onlyOnDetail();
    }
}
