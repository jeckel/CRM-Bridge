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

class SetupConnectionFormType extends AbstractType
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                child: ConfigurationKey::CARDDAV_URI->value,
                type: TextType::class,
                options: [
                    'label' => 'card_dav.field.connection_uri'
                ]
            )
            ->add(
                child: ConfigurationKey::CARDDAV_USERNAME->value,
                type: TextType::class,
                options: [
                    'label' => 'card_dav.field.connection_username'
                ]
            )
            ->add(
                child: ConfigurationKey::CARDDAV_PASSWORD->value,
                type: PasswordType::class,
                options: [
                    'label' => 'card_dav.field.connection_password'
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
