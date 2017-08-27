<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;
use AppBundle\Form\CommandeDeuxiemePageType;
use AppBundle\Form\CommandePremierePageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class BilletterieController extends Controller
{
    /**
     * @Route("/", name="homepage-billetterie")
     */
    public function indexAction(Request $request)
    {
        return $this->render('billetterie/index.html.twig');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @route("/choix-billet", name="choix-billet-billetterie")
     *
     */

    public function choixBilletAction(Request $request)
    {
        // cree la commande
        $commande = new Commande();
        // crée
        $formCommande = $this->get('form.factory')->create(CommandePremierePageType::class, $commande);
        // test la methode de request et les contenus des champs du formulaire
        if($request->isMethod('POST') && $formCommande->handleRequest($request)->isValid()) {
            // A Faire : teste le nombre de billets restants pour la date selectionnée
            // crée la variable de session commande contenant la commande
            $this->get("session")->set('commande', $commande);
            // redirige vers la seconde page du tunnel
            return $this->redirectToRoute('infos-visiteurs-billetterie');
        }
        return $this->render('billetterie/choixBillet.html.twig', array(
            'formCommande' => $formCommande->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @route("/infos-visiteurs", name="infos-visiteurs-billetterie")
     *
     */
    public function infosVisiteursAction(Request $request, Session $session)
    {

        //formulaire commande
        $commande = $this->get('session')->get('commande');
        if($commande->getBillets()->count() != $commande->getNombreBillets()) {
            for($billetACreer = 0; $billetACreer < $commande->getNombreBillets(); $billetACreer++ ) {
                $billet = new Billet();
                $commande->addBillet($billet);
            }
        }
        $formCommande = $this->get('form.factory')->create(CommandeDeuxiemePageType::class, $commande);
        if ($request->isMethod('POST') && $formCommande->handleRequest($request)->isValid()) {

            // ajoute les attributs non hydratés par le formulaire pour chaque billet ( dateVisite, type, tarifs et commande)
            foreach ($commande->getBillets() as $billet) {
                $billet->setTarif($this->determinerTarif($billet))
                    ->setCommande($commande);
                $commande->setMontantTotal($commande->getMontantTotal() + $billet->getTarif());
            }
        }
        return $this->render('billetterie/infosVisiteurs.html.twig', array(
            'formCommande' => $formCommande->createView(),
        ));
    }

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


    public function verifierDispoBillets($date) {
        // récupère le nombre de billets réservés pour la date passée en argument
        $nbBilletsReserves = $this->getDoctrine()->getRepository('AppBundle:Billet')->countNombreBillets($date);
        // si le nombre de billets reservés est supérieur a 1000, on retourne false
        if($nbBilletsReserves >= 1000) {
            $this->get('session')->getFlashBag()->add('erreur', "Tous les billets pour cette date on été vendu veuillez selectionner une autre date !");
            return false;
        }
        // si il ne reste que 10 billets on crée un message flash pour prévenir l'utilisateur
        if ($nbBilletsReserves >= 990 && $nbBilletsReserves < 1000) {
            $this->get('session')->getFlashBag()->add('warningNbBillets', "Attention, il ne reste que quelques billets pour cette date, il est possible que la commande ne puisse être validé ! ");
        }
        // si le nombre de billets reservés est inférieur a 1000, on retourne true
        return true;
    }

}