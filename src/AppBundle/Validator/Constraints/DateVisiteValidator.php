<?php


namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateVisiteValidator extends ConstraintValidator
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
        // teste si la date est passé
        // (vérifie s'il y a un écart en jours > 0 et si la différence est inversé (valeur négative de l'écart))
        if($today->diff($value)->d > 0 && $today->diff($value)->invert === 1 ) {
            $this->context->buildViolation($constraint->message)
                ->setParameters(array(
                    '{{ string }}' => "la réservation n'est pas possible les jours passés",
                    '{{ date }}' => $dateVisite))
                ->addViolation();
            return;
        }

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
                return;
            }
        }

        $jour = date('D', $value->getTimestamp());
        // teste si le jour selectionné est un dimanche
        if ($jour === 'Sun') {
            $this->context->buildViolation($constraint->message)
                ->setParameters(array(
                    '{{ string }}' => "la réservation n'est pas possible les dimanches",
                    '{{ date }}' => $dateVisite))
                ->addViolation();
            return;
        // teste si le jour selectionné est un mardi
        } elseif ($jour === 'Tue') {
            $this->context->buildViolation($constraint->message)
                ->setParameters(array(
                    '{{ string }}' => "la réservation n'est pas possible les mardis (musée fermé)",
                    '{{ date }}' => $dateVisite))
                ->addViolation();
            return;
        }

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