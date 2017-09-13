<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class JourFerieValidator extends ConstraintValidator
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
        $annee = date('Y', $value->getTimestamp());
        // récupère le tableau des jours fériés de l'année récupérée de la date
        $joursFeries = self::getHolidays($annee);
        // parcourt le tableau de jours fériés
        foreach ($joursFeries as $jourFerie) {
            // teste si la date selectionnée est un jour férié
            if($value->getTimestamp() === $jourFerie) {
                $this->context->buildViolation($constraint->message)
                    ->setParameters(array(
                        '{{ string }}' => "la réservation n'est pas possible les jours fériés",
                        '{{ date }}' => $dateVisite))
                    ->addViolation();
            }
        }
    }

    /**
     * This function returns an array of timestamp corresponding to french holidays
     * function found at http://php.net/manual/fr/function.easter-date.php
     */
    protected static function getHolidays($year = null)
    {
        if ($year === null)
        {
            $year = intval(date('Y'));
        }

        $easterDate  = easter_date($year);
        $easterDay   = date('j', $easterDate);
        $easterMonth = date('n', $easterDate);
        $easterYear   = date('Y', $easterDate);

        $holidays = array(
            // These days have a fixed date
            mktime(0, 0, 0, 1,  1,  $year),  // 1er janvier
            mktime(0, 0, 0, 5,  1,  $year),  // Fête du travail
            mktime(0, 0, 0, 5,  8,  $year),  // Victoire des alliés
            mktime(0, 0, 0, 7,  14, $year),  // Fête nationale
            mktime(0, 0, 0, 8,  15, $year),  // Assomption
            mktime(0, 0, 0, 11, 1,  $year),  // Toussaint
            mktime(0, 0, 0, 11, 11, $year),  // Armistice
            mktime(0, 0, 0, 12, 25, $year),  // Noel

            // These days have a date depending on easter
            mktime(0, 0, 0, $easterMonth, $easterDay + 2,  $easterYear), // lundi de Pâques0
            mktime(0, 0, 0, $easterMonth, $easterDay + 40, $easterYear), // jeudi de l'Ascension
            mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear), // lundi de Pentecôte
        );

        sort($holidays);

        return $holidays;
    }
}
