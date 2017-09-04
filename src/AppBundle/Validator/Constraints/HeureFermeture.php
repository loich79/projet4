<?php


namespace AppBundle\Validator\Constraints;
use Symfony\Component\Validator\Constraint;


/**
 * Class HeureFermeture
 * @package AppBundle\Validator\Constraints
 * @Annotation
 */
class HeureFermeture extends Constraint
{
    public $message = 'Vous ne pouvez plus réserver pour aujourd\'hui ({{ date }}), le musée est fermé.';
}