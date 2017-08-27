<?php


namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Tarif
 * @package AppBundle\Validator\Constraints
 * @Annotation
 */
class Tarif extends Constraint
{
    public $message = "Le tarif du billet est incorrect.";
}