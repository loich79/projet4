<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class DatePassee
 * @package AppBundle\Validator\Constraints
 * @Annotation
 */
class DatePassee extends Constraint
{
    public $message = 'Vous ne pouvez pas sélectionnée le {{ date }} : la réservation n\'est pas possible les jours passés.';
}