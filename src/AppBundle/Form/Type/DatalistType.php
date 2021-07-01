<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

/**
 * Wrapper for the EntityType to use it with HTML datalist tag
 * see AppBundle/Resources/views/form/fields.html.twig.
 */
class DatalistType extends AbstractType
{
    public function getParent()
    {
        return EntityType::class;
    }
}
