<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 21/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\EasyAdmin\Form;

use App\Infrastructure\Doctrine\Entity\Contact;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectContactFormType extends AbstractType
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                child: 'contact',
                type: EntityType::class,
                options: [
                    'label' => 'contact.field.contact',
                    'class' => Contact::class,
                    'query_builder' => static fn(EntityRepository $er): QueryBuilder =>
                        $er->createQueryBuilder('u')
                            ->orderBy('u.displayName', 'ASC'),
                    'choice_label' => static fn(Contact $contact): string =>
                        sprintf('%s <%s>', $contact->getDisplayName(), $contact->getPrimaryEmailAddress())
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
