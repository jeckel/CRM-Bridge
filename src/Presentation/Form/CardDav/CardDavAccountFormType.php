<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Form\CardDav;

use App\Infrastructure\Configuration\ConfigurationKey;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CardDavAccountFormType extends AbstractType
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
                    'label' => 'card_dav.field.name'
                ]
            )
            ->add(
                child: 'uri',
                type: TextType::class,
                options: [
                    'label' => 'card_dav.field.uri'
                ]
            )
            ->add(
                child: 'login',
                type: TextType::class,
                options: [
                    'label' => 'card_dav.field.login'
                ]
            )
            ->add(
                child: 'password',
                type: PasswordType::class,
                options: [
                    'label' => 'card_dav.field.password'
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
