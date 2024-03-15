<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 12/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\EasyAdmin\Form\Imap;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectFoldersFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                child:'folders',
                type: ChoiceType::class,
                options: [
                    'label' => 'config.imap.field.folders',
                    'choices' => $options['folders'],
                    'multiple' => true,
                    'expanded' => true
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
            'translation_domain' => 'admin',
            'folders' => []
        ]);
    }
}
