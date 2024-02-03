<?php

namespace App\Presentation\Form;

use App\Infrastructure\CardDav\AddressBookDiscovery;
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
            ->add('property', HiddenType::class, ['data' => 'carddav.default_address_book'])
            ->add('value', ChoiceType::class, ['choices' => $options['addressBooks']])
            ->add('label', HiddenType::class, ['data' => 'Default Address Book'])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Configuration::class,
            'addressBooks' => []
        ]);
    }
}
