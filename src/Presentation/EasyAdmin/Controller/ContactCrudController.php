<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\EasyAdmin\Controller;

use App\Infrastructure\Doctrine\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Override;

class ContactCrudController extends AbstractCrudController
{
    #[Override]
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    /**
     * @param string $pageName
     * @return iterable<FieldInterface>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Override]
    public function configureFields(string $pageName): iterable
    {
        if ($pageName === Crud::PAGE_INDEX) {
            yield TextField::new('displayName');
            yield EmailField::new('email', 'contact.field.primary_email')
                ->setTemplatePath('admin/field/contact_primary_email.html.twig');
            yield TelephoneField::new('phoneNumber');
            yield AssociationField::new('company');
            yield TextField::new('addressBook');
            yield AssociationField::new('mails');
        }
        if ($pageName === Crud::PAGE_DETAIL) {
            yield FormField::addTab('contact.tab.summary', 'fas fa-id-card');
            yield TextField::new('displayName');
            yield EmailField::new('email', 'contact.field.primary_email')
                ->setTemplatePath('admin/field/contact_primary_email.html.twig');
            yield TextField::new('email', 'contact.field.secondary_emails')
                ->setTemplatePath('admin/field/contact_secondary_emails.html.twig');
            yield TextField::new('firstName');
            yield TextField::new('lastName');
            yield TelephoneField::new('phoneNumber');
            yield AssociationField::new('company');
            yield AssociationField::new('addressBook');

            yield FormField::addTab('contact.tab.mails', 'fa fa-inbox');
            yield AssociationField::new('mails')
                ->setLabel(false)
                ->setSortProperty('date')
                ->setTemplatePath('admin/field/contact_mails.html.twig');
            yield FormField::addTab('contact.tab.activities', 'fas fa-bell');
            yield AssociationField::new('activities')
                ->setLabel(false)
                ->setSortProperty('date')
                ->setTemplatePath('admin/field/contact_activities.html.twig');
        }

        if ($pageName === Crud::PAGE_EDIT || $pageName === Crud::PAGE_NEW) {
            yield TextField::new('displayName');
            yield TextField::new('firstName');
            yield TextField::new('lastName');
            yield TelephoneField::new('phoneNumber');
            yield AssociationField::new('company')
                ->renderAsEmbeddedForm(CompanyCrudController::class, 'create_company_from_contact')
                ->setCrudController(CompanyCrudController::class)
            ;
        }
    }
}
