<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class DateVisite
 * @package AppBundle\Validator\Constraints
 * @Annotation
 */
class DateVisite extends Constraint
{
    public $message = 'Vous ne pouvez pas sélectionnée le {{ date }} : {{ string }}.';
}