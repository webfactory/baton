<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Package;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SearchPackageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('package', DatalistType::class, [
                'label' => 'Package name',
                'class' => Package::class,
                'choice_label' => 'name',
                'choice_value' => 'name',
                'query_builder' => function (EntityRepository $repo) {
                    return $repo->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
                'placeholder' => 'First choose a package',
                'choice_translation_domain' => false,
                'label_attr' => ['class' => 'sr-only'],
                'constraints' => [new NotBlank()],
            ])
            ->add('versionConstraintOperator', ChoiceType::class, [
                'label' => 'Version constraint operator',
                'label_attr' => ['class' => 'sr-only'],
                'choices' => ['<' => '<', '<=' => '<=', '>' => '>', '>=' => '>=', '==' => '==', 'all' => 'all'],
                'mapped' => false,
                'choice_translation_domain' => false,
                'placeholder' => 'Version constraint operator',
                'disabled' => true,
            ])
            ->add('versionConstraintValue', ChoiceType::class, [
                'label' => 'Version constraint value',
                'label_attr' => ['class' => 'sr-only'],
                'placeholder' => 'Version constraint value',
                'choice_translation_domain' => false,
                'choices' => [],
                'mapped' => false,
                'disabled' => true,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('translation_domain', false);
    }
}
