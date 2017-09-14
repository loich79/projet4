<?php


namespace AppBundle\Utils;


use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class GestionnaireCommande
{
    private $session;
    private $calculateurTarif;
    private $correcteurType;
    private $em;

    public function __construct(Session $session, CalculateurTarif $calculateurTarif, CorrecteurType $correcteurType, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->calculateurTarif = $calculateurTarif;
        $this->correcteurType = $correcteurType;
        $this->em = $em;
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
        $nombreBillets = $commande->getNombreBillets();

        if($commande->getBillets()->count() != $nombreBillets) {
            for($billetACreer = 0; $billetACreer < $n; $billetACreer++ ) {
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
        $commande->setCodeReservation($codeReservation)->setDateReservation(new \DateTime());
    }
    public function enregistrerCommande($commande)
    {
        //enregistre la commande en bdd
        $this->em->persist($commande);
        $this->em->flush();
    }
}
