<?php

namespace App\Presentation\Form\CardDav;

use App\Infrastructure\Configuration\ConfigurationKey;
use App\Infrastructure\Doctrine\Entity\Configuration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultAddressBookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                child: 'address_books',
                type: ChoiceType::class,
                options: [
                    'choices' => $options['addressBooks'],
                    'label' => 'card_dav.field.default_address_book',
                    'multiple' => true,
                ]
            )
            ->add(
                child:'save',
                type: SubmitType::class,
                options: ['label' => 'action.select']
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'addressBooks' => [],
            'translation_domain' => 'admin'
        ]);
    }
}
