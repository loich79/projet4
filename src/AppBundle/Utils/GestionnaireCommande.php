<?php


namespace AppBundle\Utils;


use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class GestionnaireCommande
 * @package AppBundle\Utils
 */
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

    /**
     * prépare la commande en l'ajoutant a la session
     * @param Commande $commande
     */
    public function initialiserCommandePageChoixVisite(Commande $commande)
    {
        $this->session->set('commande', $commande);
    }

    /**
     * déclenche la correction du type
     * @param Commande $commande
     */
    public function traiterCommandePageChoixVisite(Commande $commande)
    {
        // appelle du service de correction automatique du type
        $this->correcteurType->corrigerType($commande);
    }

    /**
     * prépare la commande en créant les billets
     * @param Commande $commande
     */
    public function initialiserCommandePageInfosVisiteurs(Commande $commande)
    {
        $commande->setMontantTotal(0);
        $nombreBillets = $commande->getNombreBillets();

        if($commande->getBillets()->count() != $nombreBillets) {
            for($billetACreer = 0; $billetACreer < $nombreBillets; $billetACreer++ ) {
                $billet = new Billet();
                $commande->addBillet($billet);
            }
        }
    }

    /**
     * ajoute la commande aux billets et calcule le montant total de la commande
     * @param Commande $commande
     * @return bool
     */
    public function traiterCommandePageInfosVisiteurs(Commande $commande)
    {
        foreach ($commande->getBillets() as $billet) {
            $billet->setTarif($this->calculateurTarif->determinerTarif($billet))
                ->setCommande($commande);
            $commande->setMontantTotal($commande->getMontantTotal() + $billet->getTarif());
        }
    }

    /**
     * ajoute le token comme code de réservation a la commande et la date du jour comme date de reservation
     * @param Commande $commande
     * @param string $token
     */
    public function traiterCommandePageRetourPaiement(Commande $commande, string $token)
    {
        $codeReservation = str_replace('tok_','',$token);
        $codeReservation = strtoupper($codeReservation);
        $commande->setCodeReservation($codeReservation)->setDateReservation(new \DateTime());
    }

    /**
     * enregistre la commande en base de données
     * @param $commande
     */
    public function enregistrerCommande($commande)
    {
        //enregistre la commande en bdd
        $this->em->persist($commande);
        $this->em->flush();
    }
}
