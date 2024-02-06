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

class SetupFormType extends AbstractType
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                child: ConfigurationKey::IMAP_HOST->value,
                type: TextType::class,
                options: [
                    'label' => 'imap.field.host'
                ]
            )
            ->add(
                child: ConfigurationKey::IMAP_LOGIN->value,
                type: TextType::class,
                options: [
                    'label' => 'imap.field.login'
                ]
            )
            ->add(
                child: ConfigurationKey::IMAP_PASSWORD->value,
                type: PasswordType::class,
                options: [
                    'label' => 'imap.field.password'
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
