<?php


namespace AppBundle\Utils;


use AppBundle\Entity\Billet;

class CalculateurTarif
{
    /**
     * Determine le tarif d'un billet en fonction de l'age du visiteur et du tarif réduit
     * @param Billet $billet
     * @return int
     */
    public function determinerTarif(Billet $billet) {
        $today = new \DateTime();
        $age = $billet->getDateNaissance()->diff($today)->y;
        if ($billet->getTarifReduit() && $age>=12) {
            $tarif = 10;
        } else {
            if ($age<4) {
                $tarif = 0;
            } elseif ($age >= 4 && $age < 12) {
                $tarif = 8;
            } elseif ($age >= 12 && $age < 60) {
                $tarif = 16;
            } else {
                $tarif = 12;
            }
        }
        return $tarif;
    }
}
