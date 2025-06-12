<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\VersionConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Throwable;
use TypeError;

class VersionConstraintType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('operator', ChoiceType::class, [
                'label' => 'Version constraint operator',
                'label_attr' => ['class' => 'sr-only'],
                'choices' => ['<' => '<', '<=' => '<=', '>' => '>', '>=' => '>=', '==' => '==', 'all' => 'all'],
                'choice_translation_domain' => false,
                'placeholder' => 'Version constraint operator',
                'required' => true,
                'constraints' => [new NotBlank()],
            ])
            ->add('value', ChoiceType::class, [
                'label' => 'Version constraint value',
                'label_attr' => ['class' => 'sr-only'],
                'placeholder' => 'Version constraint value',
                'choice_translation_domain' => false,
                'choices' => [], // empty list: real values will be set dynamically via JavaScript
                'required' => false,
            ]);

        $builder->get('value')->resetViewTransformers(); // hack to disable validation against empty list

        $builder->addModelTransformer(new class implements DataTransformerInterface {
            public function transform($value)
            {
                if (null === $value) {
                    return null;
                }

                if (!$value instanceof VersionConstraint) {
                    throw new TypeError('Got '.gettype($value));
                }

                return [
                    'operator' => $value->getOperator(),
                    'value' => $value->getNormalizedVersionString(),
                ];
            }

            public function reverseTransform($value)
            {
                if (null === $value) {
                    return null;
                }

                try {
                    return new VersionConstraint($value['operator'], $value['value']);
                } catch (Throwable $exception) {
                    throw new TransformationFailedException($exception->getMessage(), 0, $exception);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
