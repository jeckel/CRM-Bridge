<?php

namespace App\Presentation\Form\CardDav;

use App\Infrastructure\Configuration\ConfigurationKey;
use App\Infrastructure\Doctrine\Entity\Configuration;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultAddressBookFormType extends AbstractType
{
    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                child: 'syncedAddressBooks',
                type: ChoiceType::class,
                options: [
                    'choices' => $options['addressBooks'],
                    'label' => 'setup.card_dav.field.sync_address_books',
                    'multiple' => true,
                    'expanded' => true
                ]
            )
            ->add(
                child: 'defaultAddressBook',
                type: ChoiceType::class,
                options: [
                    'choices' => $options['addressBooks'],
                    'label' => 'setup.card_dav.field.default_address_book',
                    'multiple' => false,
                ]
            )
            ->add(
                child:'save',
                type: SubmitType::class,
                options: ['label' => 'action.select']
            )
        ;
    }

    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'addressBooks' => [],
//            'translation_domain' => 'admin',
            'hx-post' => null
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
