<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;
use AppBundle\Form\CommandeDeuxiemePageType;
use AppBundle\Form\CommandePremierePageType;
use AppBundle\Utils\CalculateurTarif;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Stripe\Charge;
use Stripe\Stripe;
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
        if($request->isMethod('POST') && $formCommande->handleRequest($request)->isValid() && $this->verifierDispoBillets($commande->getDateVisite())) {
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
     * @param Session $session
     * @param CalculateurTarif $calculateurTarif
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @route("/infos-visiteurs", name="infos-visiteurs-billetterie")
     */
    public function infosVisiteursAction(Request $request, Session $session, CalculateurTarif $calculateurTarif)
    {

        //formulaire commande
        $commande = $this->get('session')->get('commande');
        // réinitialise le montant total pour éviter les erreurs de calcul du montant total (ex: retour en arriere sur le navigateur)
        $commande->setMontantTotal(0);
        if($commande->getBillets()->count() != $commande->getNombreBillets()) {
            for($billetACreer = 0; $billetACreer < $commande->getNombreBillets(); $billetACreer++ ) {
                $billet = new Billet();
                $commande->addBillet($billet);
            }
        }
        dump($this->get('session')->get('commande'));
        $formCommande = $this->get('form.factory')->create(CommandeDeuxiemePageType::class, $commande);
        if ($request->isMethod('POST') && $formCommande->handleRequest($request)->isValid()) {
            $formCommande->isValid();
            // ajoute les attributs non hydratés par le formulaire pour chaque billet ( dateVisite, type, tarifs et commande)
            foreach ($commande->getBillets() as $billet) {
                $billet->setTarif($calculateurTarif->determinerTarif($billet))
                    ->setCommande($commande);
                $commande->setMontantTotal($commande->getMontantTotal() + $billet->getTarif());
            }
            dump($this->get('session')->get('commande'));
            return $this->redirectToRoute('paiement-billetterie');
        }
        return $this->render('billetterie/infosVisiteurs.html.twig', array(
            'formCommande' => $formCommande->createView(),
        ));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @route("/paiement", name="paiement-billetterie")
     */
    public function paiementAction(Request $request)
    {
        return $this->render('billetterie/paiement.html.twig');
    }

    /**
     * @Route(
     *     "/retour-paiment",
     *     name="retour-paiement-billetterie",
     *     methods="POST"
     * )
     */
    public function retourPaiementAction()
    {
        Stripe::setApiKey("sk_test_z8jlJCKrSKN0U59keBnd272U");

        // Get the credit card details submitted by the form
        $token = $_POST['stripeToken'];

        // Create a charge: this will charge the user's card
        try {
            $charge = Charge::create(array(
                "amount" => ($this->get('session')->get('commande')->getMontantTotal()*100), // Amount in cents
                "currency" => "eur",
                "source" => $token,
                "description" => "Paiement Stripe - OpenClassrooms Exemple"
            ));
            // ajoute le token comme code de réservation a la commande et la date du jour comme date de reservation
            $codeReservation = str_replace('tok_','',$token);
            $codeReservation = strtoupper($codeReservation);
            $this->get('session')->get('commande')->setCodeReservation($codeReservation)->setDateReservation(new \DateTime());
            return $this->redirectToRoute("confirmation-paiement-billetterie");
        } catch(\Stripe\Error\Card $e) {

            $this->get('session')->getFlashBag()->add("erreur","Une erreur est survenue, veuillez réessayer");
            return $this->redirectToRoute("paiement-billetterie");
            // The card has been declined
        }
    }

    /**
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param Request $request
     * @Route("/confirmation-paiement", name="confirmation-paiement-billetterie")
     */
    public function confirmationPaiementAction(\Swift_Mailer $mailer)
    {
        $commande = $this->get('session')->get('commande');
        //enregistre la commande en bdd
        $em = $this->getDoctrine()->getManager();
        $em->persist($commande);
        $em->flush();
        //envoie du mail de confirmation
        $message = (new \Swift_Message('confirmation de commande'))
            ->setFrom('lhay17@gmail.com')
            ->setTo($commande->getEmail())
            ->setBody($this->renderView('Billetterie/Emails/confirmationCommande.html.twig'),'text/html');
        $mailer->send($message);
        // affiche la page de confirmation de paiement
        return $this->render('billetterie/confirmationPaiement.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/affichage-email", name="affichage-email")
     */
    public function affichageEmailAction()
    {
        return $this->render('billetterie/Emails/confirmationCommande.html.twig');
    }

    /**
     * @param $date
     * @return bool
     */
    public function verifierDispoBillets($date) {
        // récupère le nombre de billets réservés pour la date passée en argument
        $nbBilletsReserves = $this->getDoctrine()->getRepository('AppBundle:Billet')->countNombreBillets($date);
        dump($nbBilletsReserves);
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