<?php

namespace App\Presentation\Form;

use App\Infrastructure\CardDav\AddressBookDiscovery;
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
            ->add('property', HiddenType::class, ['data' => ConfigurationKey::CARDDAV_DEFAULT_ADDRESS_BOOK->value])
            ->add(
                child: 'value',
                type: ChoiceType::class,
                options: [
                    'choices' => $options['addressBooks'],
                    'label' => 'address_book.field.default_address_book',
                ]
            )
            ->add(
                child: 'label',
                type: HiddenType::class,
                options: [
                    'data' => 'Default Address Book',
                ]
            )
            ->add(
                child:'save',
                type: SubmitType::class,
                options: ['label' => 'address_book.field.default_address_book_submit']
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Configuration::class,
            'addressBooks' => [],
            'translation_domain' => 'admin'
        ]);
    }
}
