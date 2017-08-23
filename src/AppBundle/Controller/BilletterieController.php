<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;
use AppBundle\Entity\Tarif;
use AppBundle\Form\BilletDeuxiemePageType;
use AppBundle\Form\BilletPremierePageType;
use AppBundle\Form\BilletType;
use AppBundle\Form\CommandeDeuxiemePageType;
use AppBundle\Form\CommandeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

class BilletterieController extends Controller
{
    /**
     * @Route("/", name="homepage-billetterie")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
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
        $billet = new Billet();
        $formBillet = $this->get('form.factory')->create(BilletPremierePageType::class, $billet);
        // test la methode de request et les contenus des champs du formulaire
        // TEST à ajouter : tester la date (date du jour, pas un mardi ni un dimanche ni un jour férié)
        // tester le nombre de billets reservés pour cette date (doit etre inférieur à 1000)
        if($request->isMethod('POST') && $formBillet->handleRequest($request)->isValid()) {
            // crée 3 variables de session contenant le nombre de billets, le type de billets et la date de visite
            $this->get("session")->set('nombreBillets', $billet->getCommande()->getNombreBillets());
            $this->get("session")->set('dateVisite', $billet->getDateVisite());
            $this->get("session")->set('type', $billet->getType());
            // redirige vers la seconde page du tunnel
            return $this->redirectToRoute('infos-visiteurs-billetterie');
        }
        return $this->render('billetterie/choixBillet.html.twig', array(
            'formBillet' => $formBillet->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @route("/infos-visiteurs", name="infos-visiteurs-billetterie")
     *
     */
    public function infosVisiteursAction(Request $request)
    {
        //formulaire commande
        $commande = new Commande();
        $formCommande = $this->get('form.factory')->create(CommandeDeuxiemePageType::class, $commande);
        if ($request->isMethod('POST') && $formCommande->handleRequest($request)->isValid()) {
            // ajoute l'attribut nombreBillets a la commande
            $commande->setNombreBillets($this->get('session')->get('nombreBillets'));
            // enregistre la commande en variable de session
            $this->get('session')->set('commande', $commande);
            // ajoute les attributs non hydratés par le formulaire pour chaque billet ( dateVisite, type, tarifs et commande)
            foreach ($commande->getBillets() as $billet) {
                $billet->setDateVisite($this->get('session')->get('dateVisite'))
                    ->setType($this->get('session')->get('type'))
                    ->setTarif($this->determinerTarif($billet))
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

}