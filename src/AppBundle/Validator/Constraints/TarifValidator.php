<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TarifValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        if($value !== 0 && $value !== 8 && $value !== 10 && $value !== 12 && $value !== 16) {
            $this->context->addViolation($constraint->message);
        }
    }
}