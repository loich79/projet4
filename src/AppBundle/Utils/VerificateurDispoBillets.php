<?php


namespace AppBundle\Utils;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class VerificateurDispoBillets
{
    private $em;
    private $session;

    public function __construct(EntityManagerInterface $em, Session $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    public function verifierDispoBillets($date)
    {
        // récupère le nombre de billets réservés pour la date passée en argument
        $nbBilletsReserves = $this->em->getRepository('AppBundle:Billet')->countNombreBillets($date);
        // si le nombre de billets reservés est supérieur a 1000, on retourne false
        if($nbBilletsReserves >= 1000) {
            $this->session->getFlashBag()->add('erreur', "Tous les billets pour cette date ont été vendus. Veuillez selectionner une autre date !");
            return false;
        }
        // si il ne reste que 10 billets on crée un message flash pour prévenir l'utilisateur
        if ($nbBilletsReserves >= 990 && $nbBilletsReserves < 1000) {
            $this->session->getFlashBag()->add('warningNbBillets', "Attention, il ne reste que quelques billets pour cette date, il est possible que la commande ne puisse être validée ! ");
        }
        // si le nombre de billets reservés est inférieur a 1000, on retourne true
        return true;
    }
}
