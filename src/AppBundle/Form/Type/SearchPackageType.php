<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Package;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
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
                'required' => true,
                'constraints' => [new NotBlank()],
            ])
            ->add('versionConstraint', VersionConstraintType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('translation_domain', false);
    }
}
