<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class Type
 * @package AppBundle\Validator\Constraints
 * @Annotation
 */
class Type extends Constraint
{
    public $message = 'Le type de billets doit être "journée" ou "demi-journée".';
}
