<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Form\Imap;

use App\Infrastructure\Configuration\ConfigurationKey;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImapAccountFormType extends AbstractType
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                child: 'name',
                type: TextType::class,
                options: [
                    'label' => 'imap.field.label'
                ]
            )
            ->add(
                child: 'uri',
                type: TextType::class,
                options: [
                    'label' => 'imap.field.host'
                ]
            )
            ->add(
                child: 'login',
                type: TextType::class,
                options: [
                    'label' => 'imap.field.login'
                ]
            )
            ->add(
                child: 'password',
                type: PasswordType::class,
                options: [
                    'label' => 'imap.field.password'
                ]
            )
            ->add(
                child:'save',
                type: SubmitType::class,
                options: ['label' => 'action.save', 'translation_domain' => 'admin']
            );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'webmail'
        ]);
    }
}
