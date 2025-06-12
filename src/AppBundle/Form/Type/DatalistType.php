<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

/**
 * Wrapper for the EntityType to use it with HTML datalist tag
 * see AppBundle/Resources/views/form/fields.html.twig.
 *
 * As soon as we have Symfony 4, all usages of this type can be replaced with EntityType + ['block_prefix' => 'datalist']
 *
 * @see https://symfony.com/doc/4.x/form/form_themes.html#custom-fragment-naming-for-individual-fields
 *
 * @see ../../Resources/views/Form/fields.html.twig
 */
class DatalistType extends AbstractType
{
    public function getParent()
    {
        return EntityType::class;
    }
}
