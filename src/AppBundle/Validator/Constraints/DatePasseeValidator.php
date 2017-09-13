<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DatePasseeValidator extends ConstraintValidator
{

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        $dateVisite = $value->format("d/m/Y");
        $today = new \DateTime();
        // teste si la date est passée
        // (vérifie s'il y a un écart en jours > 0 et si la différence est inversé (valeur négative de l'écart))
        if($today->diff($value)->d > 0 && $today->diff($value)->invert === 1 ) {
            $this->context->buildViolation($constraint->message)
                ->setParameters(array(
                    '{{ date }}' => $dateVisite))
                ->addViolation();
        }
    }
}
