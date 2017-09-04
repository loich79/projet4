<?php


namespace AppBundle\Validator\Constraints;
use Symfony\Component\Validator\Constraint;


/**
 * Class Dimanche
 * @package AppBundle\Validator\Constraints
 * @Annotation
 */
class Dimanche extends Constraint
{
    public $message = 'Vous ne pouvez pas sélectionnée le {{ date }} : la réservation n\'est pas possible les dimanches.';
}