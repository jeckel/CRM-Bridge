<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Contact\Form;

use App\Infrastructure\Doctrine\EntityModel\CardDavAddressBook;
use App\Infrastructure\Doctrine\EntityModel\Company;
use App\Infrastructure\Doctrine\Repository\CompanyRepository;
use Doctrine\ORM\QueryBuilder;
use Override;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                child: 'displayName',
                type: TextType::class,
                options: [
                    'label' => 'contact.field.display_name'
                ]
            )
            ->add(
                child: 'firstName',
                type: TextType::class,
                options: [
                    'label' => 'contact.field.first_name',
                    'required' => false
                ]
            )
            ->add(
                child: 'lastName',
                type: TextType::class,
                options: [
                    'label' => 'contact.field.last_name',
                    'required' => false
                ]
            )
            ->add(
                child: 'email',
                type: EmailType::class,
                options: [
                    'label' => 'contact.field.email_address'
                ]
            )
            ->add(
                child: 'company',
                type: EntityType::class,
                options: [
                    'class' => Company::class,
                    'label' => 'contact.field.existing_company',
                    'query_builder' => fn(CompanyRepository $er): QueryBuilder => $er->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC')
                    ,
                    'required' => false,
                ]
            )
            ->add(
                child: 'companyNew',
                type: TextType::class,
                options: [
                    'label' => 'contact.field.new_company',
                    'required' => false
                ]
            )
            ->add(
                child: 'addressBook',
                type: EntityType::class,
                options: [
                    'class' => CardDavAddressBook::class,
                    'label' => 'contact.field.address_book',
                    'required' => true,
                ]
            )
            ->add(
                child:'save',
                type: SubmitType::class,
                options: ['label' => 'action.save']
            );
    }

    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'hx-post' => null,
        ]);
    }

    #[Override]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if ($options['hx-post'] !== null) {
            $view->vars['attr']['hx-post'] = $options['hx-post'];
        }
        parent::buildView($view, $form, $options); // TODO: Change the autogenerated stub
    }
}
