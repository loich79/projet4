<?php


namespace AppBundle\Utils;


use AppBundle\Entity\Commande;
use Symfony\Component\HttpFoundation\Session\Session;

class CorrecteurType
{
    private $session;
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * change le type de billets en "demi-journée" si la date de visite est aujourd'hui et si l'heure est supérieur à 14h
     * @param Commande $commande
     */
    public function corrigerType(Commande $commande) {
        // test si la date selectionné est aujourd'hui et si l'heure actuel est supérieur à 14h
        $today = new \DateTime();
        $heureToday = (int) date('H', $today->getTimestamp());
        $today = date_format($today,  "d/m/Y");
        $dateSelectionnee = date_format($commande->getDateVisite(), "d/m/Y");
        if($dateSelectionnee === $today && $heureToday >= 14) {
            $commande->setType('demi-journee');
            $this->session->getFlashBag()->add('notice', "Le type de billet a été automatiquement modifié car à partir de 14h, les billets vendus pour aujourd'hui sont de type demi-journée.");
        }
    }
}
