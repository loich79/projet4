<?php


namespace AppBundle\Utils;


use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;
use Symfony\Component\HttpFoundation\Session\Session;

class GestionnaireCommande
{
    private $session;
    private $calculateurTarif;
    private $correcteurType;

    public function __construct(Session $session, CalculateurTarif $calculateurTarif, CorrecteurType $correcteurType)
    {
        $this->session = $session;
        $this->calculateurTarif = $calculateurTarif;
        $this->correcteurType = $correcteurType;
    }
    public function initialiserCommandePageChoixVisite(Commande $commande)
    {
        $this->session->set('commande', $commande);
    }

    public function traiterCommandePageChoixVisite(Commande $commande)
    {
        // appelle du service de correction automatique du type
        $this->correcteurType->corrigerType($commande);
    }
    public function initialiserCommandePageInfosVisiteurs(Commande $commande)
    {
        $commande->setMontantTotal(0);

        if($commande->getBillets()->count() != $commande->getNombreBillets()) {
            for($billetACreer = 0; $billetACreer < $commande->getNombreBillets(); $billetACreer++ ) {
                $billet = new Billet();
                $commande->addBillet($billet);
            }
        }
    }

    public function traiterCommandePageInfosVisiteurs(Commande $commande)
    {
        foreach ($commande->getBillets() as $billet) {
            $billet->setTarif($this->calculateurTarif->determinerTarif($billet))
                ->setCommande($commande);
            $commande->setMontantTotal($commande->getMontantTotal() + $billet->getTarif());
        }
    }

    public function traiterCommandePageRetourPaiement(Commande $commande, string $token)
    {
        // ajoute le token comme code de réservation a la commande et la date du jour comme date de reservation
        $codeReservation = str_replace('tok_','',$token);
        $codeReservation = strtoupper($codeReservation);
        $this->session->get('commande')->setCodeReservation($codeReservation)->setDateReservation(new \DateTime());
    }
}