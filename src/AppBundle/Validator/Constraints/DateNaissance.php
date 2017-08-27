<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class DateNaissance
 * @package AppBundle\Validator
 *
 * @Annotation
 */
class DateNaissance extends Constraint
{
    public $message = "La date de naissance doit être une date passée.";
}