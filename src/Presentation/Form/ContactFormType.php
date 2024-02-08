<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 07/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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
                    'label' => 'contact.field.email'
                ]
            )
            ->add(
                child: 'company',
                type: TextType::class,
                options: [
                    'label' => 'contact.field.company',
                    'required' => false
                ]
            )
            ->add(
                child:'save',
                type: SubmitType::class,
                options: ['label' => 'action.save']
            );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'admin'
        ]);
    }
}
