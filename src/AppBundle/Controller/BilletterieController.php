<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;
use AppBundle\Entity\Tarif;
use AppBundle\Form\BilletPremierePageType;
use AppBundle\Form\BilletType;
use AppBundle\Form\CommandeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        // tester le nombre de billets reserver pour cette date (doit etre inférieur à 1000)
        if($request->isMethod('POST') && $formBillet->handleRequest($request)->isValid()) {
            $this->get("session")->set('nombreBillets', $billet->getCommande()->getNombreBillets());
            $this->get("session")->set('dateVisite', $billet->getDateVisite());
            $this->get("session")->set('type', $billet->getType());
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
        return $this->render('billetterie/infosVisiteurs.html.twig', array(
        ));
    }
}