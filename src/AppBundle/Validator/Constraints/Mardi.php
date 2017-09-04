<?php


namespace AppBundle\Validator\Constraints;
use Symfony\Component\Validator\Constraint;


/**
 * Class Mardi
 * @package AppBundle\Validator\Constraints
 * @Annotation
 */
class Mardi extends Constraint
{
    public $message = 'Vous ne pouvez pas sélectionnée le {{ date }} : la réservation n\'est pas possible les mardis (musée fermé).';
}