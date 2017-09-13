<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class HeureFermetureValidator extends ConstraintValidator
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
        $jour = date('D', $value->getTimestamp());
        $heureToday = date('H', $today->getTimestamp()) ;
        $minuteToday = date('i', $today->getTimestamp());
        $heureFermetureNormal = "17";
        $heureFermetureNocturne = "21";
        $minuteFermeture = "30";
        // formatage de la date du jour et de la date saisie dans le formulaire pour pouvoir les comparer
        $valueFormatee= date_format($value,'d/m/Y');
        $todayFormatee = date_format($today, 'd/m/Y');
        // teste retournant un violation du validateur si l'heure de fermeture est dépassé et si la date selectionné est aujourd'hui
        if ($todayFormatee === $valueFormatee) {
            if ($jour === 'Wen' || $jour === 'Fri') {
                if ($heureToday > $heureFermetureNocturne || ($heureToday === $heureFermetureNocturne && $minuteToday >= $minuteFermeture)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameters(array(
                            '{{ string }}' => "Vous ne pouvez plus réserver pour aujourd'hui, le musée est fermé",
                            '{{ date }}' => $dateVisite))
                        ->addViolation();
                    return;
                }
            } else {
                if ($heureToday > $heureFermetureNormal || ($heureToday === $heureFermetureNormal && $minuteToday >= $minuteFermeture)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameters(array(
                            '{{ string }}' => "Vous ne pouvez plus réserver pour aujourd'hui, le musée est fermé",
                            '{{ date }}' => $dateVisite))
                        ->addViolation();
                    return;
                }
            }
        }
    }
}
