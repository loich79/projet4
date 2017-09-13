<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class JourFerie
 * @package AppBundle\Validator\Constraints
 *
 * @Annotation
 */
class JourFerie extends Constraint
{
    public $message = 'Vous ne pouvez pas sélectionnée le {{ date }} : la réservation n\'est pas possible les jours fériés.';
}
