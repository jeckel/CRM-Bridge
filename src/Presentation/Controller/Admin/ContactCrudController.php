<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\Company;
use App\Infrastructure\Doctrine\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Override;
use Ramsey\Uuid\Uuid;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->hideNullValues();
    }

    public function configureActions(Actions $actions): Actions
    {
        $syncVCard = Action::new('sync_vcard', 'card_dav.action.sync')
            ->linkToRoute(routeName: 'carddav_sync')
            ->setIcon('fa fa-sync')
            ->setCssClass('btn btn-success')
            ->createAsGlobalAction();
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $syncVCard)
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
        if ($pageName === Crud::PAGE_INDEX) {
            yield TextField::new('displayName');
            yield EmailField::new('email');
            yield TelephoneField::new('phoneNumber');
            yield AssociationField::new('company');
            yield TextField::new('addressBook');
            yield AssociationField::new('mails');
        }
        if ($pageName === Crud::PAGE_DETAIL) {
            yield FormField::addTab('sommaire');
            yield TextField::new('displayName');
            yield EmailField::new('email');
            yield TextField::new('firstName');
            yield TextField::new('lastName');
            yield TelephoneField::new('phoneNumber');
            yield AssociationField::new('company');
            yield TextField::new('addressBook');

            yield FormField::addTab('Mails');
            yield AssociationField::new('mails')
                ->setSortProperty('date')
                ->setTemplatePath('admin/field/contact_mails.html.twig');
        }

        if ($pageName === Crud::PAGE_EDIT || $pageName === Crud::PAGE_NEW) {
            yield TextField::new('displayName');
            yield EmailField::new('email');
            yield TextField::new('firstName');
            yield TextField::new('lastName');
            yield TelephoneField::new('phoneNumber');
            yield AssociationField::new('company')
                ->renderAsEmbeddedForm(CompanyCrudController::class, 'create_company_from_contact')
                ->setCrudController(CompanyCrudController::class)
            ;
        }
    }

    /**
     * @return Contact
     */
    #[Override]
    public function createEntity(string $entityFqcn)
    {
        /** @var Contact $contact */
        $contact = parent::createEntity($entityFqcn);
        $contact->setId(Uuid::uuid4()->toString());
        return $contact;
    }

    /**
     * @param mixed $entityInstance
     */
    #[Override]
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Contact $entityInstance */
        if ($entityInstance->getCompany() !== null && !$entityInstance->getCompany()->hasId()) {
            $company = $entityManager->getRepository(Company::class)
                ->findOneBy(['name' => $entityInstance->getCompany()->getName()]);

            (null === $company) ?
                $entityInstance->getCompany()->setId(Uuid::uuid4()->toString()) :
                $entityInstance->setCompany($company)
            ;
        }
        parent::persistEntity($entityManager, $entityInstance); // TODO: Change the autogenerated stub
    }
}
