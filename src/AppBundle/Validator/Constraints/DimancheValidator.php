<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DimancheValidator extends ConstraintValidator
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
        $jour = date('D', $value->getTimestamp());
        if ($jour === 'Sun') {
            $this->context->buildViolation($constraint->message)
                ->setParameters(array(
                    '{{ date }}' => $dateVisite))
                ->addViolation();
        }
    }
}
