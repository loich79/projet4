<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Commande;
use AppBundle\Form\CommandeDeuxiemePageType;
use AppBundle\Form\CommandePremierePageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Stripe\Charge;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class BilletterieController extends Controller
{
    /**
     * @Route("/", name="homepage-billetterie")
     */
    public function indexAction(Request $request)
    {
        // affiche le page index.html.twig
        return $this->render('billetterie/index.html.twig');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @route("/choix-visite", name="choix-visite-billetterie")
     *
     */
    public function choixVisiteAction(Request $request)
    {
        // cree la commande
        $commande = new Commande();
        // crée le formulaire pour la commande
        $formCommande = $this->get('form.factory')->create(CommandePremierePageType::class, $commande);

        if($this->get('session')->has('commande') === false && $request->isMethod('POST')) {
            return $this->redirectToRoute('session-expiree-billetterie');
        }
        $this->get('app.gestionnairecommande')->initialiserCommandePageChoixVisite($commande);
        // test la methode de request et les contenus des champs du formulaire et la dispo des billets pour la date selectionnée
        if ($request->isMethod('POST') && $formCommande->handleRequest($request)->isValid()
            && $this->get('app.verificateurdispobillets')->verifierDispoBillets($commande->getDateVisite())
        ) {
            // traitement sur la commande : correction automatique du type et ajout de la commande en session
            $this->get('app.gestionnairecommande')->traiterCommandePageChoixVisite($commande);
            //indique que l'etape est validée
            $this->get('session')->set('etapeValidee', 'choix-visite');
            // redirige vers la seconde page du tunnel
            return $this->redirectToRoute('infos-visiteurs-billetterie');
        }

        // affiche la page choixVisite.html.twig
        return $this->render('Billetterie/choixVisite.html.twig', array(
            'formCommande' => $formCommande->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @route("/infos-visiteurs", name="infos-visiteurs-billetterie")
     */
    public function infosVisiteursAction(Request $request, Session $session)
    {
       if($session->get("etapeValidee") != 'choix-visite'
           && $session->get("etapeValidee") != 'paiement'
           && $session->get("etapeValidee") != 'infos-visiteurs') {
            throw new \Exception('Vous ne pouvez pas accéder à cette page.');
        }
        if($session->has('commande') === false) {
            return $this->redirectToRoute('session-expiree-billetterie');
        }
        // récupère la commande depuis la session
        $commande = $session->get('commande');
        // instancie le gestionnaire de commande
        $gestionnaireCommande = $this->get("app.gestionnairecommande");
        // réinitialise le montant total pour éviter les erreurs de calcul du montant total (ex: retour en arriere sur le navigateur)
        // et ajoute les billets en fonction du nombre de billets selectionné a la page précédente
        $gestionnaireCommande->initialiserCommandePageInfosVisiteurs($commande);
        // crée le formulaire pour la commande
        $formCommande = $this->get('form.factory')->create(CommandeDeuxiemePageType::class, $commande);
        if ($request->isMethod('POST') && $formCommande->handleRequest($request)->isValid()) {
            // ajoute les attributs non hydratés par le formulaire pour chaque billet ( dateVisite, type, tarifs et commande)
            // et calcule le montant total
            $gestionnaireCommande->traiterCommandePageInfosVisiteurs($commande);
            //indique que l'etape est validée
            $session->set('etapeValidee', 'infos-visiteurs');

            // redirige vers la page de paiement
            return $this->redirectToRoute('paiement-billetterie');
        }
        // affiche la page infosVisiteurs.html.twig
        return $this->render('billetterie/infosVisiteurs.html.twig', array(
            'formCommande' => $formCommande->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @route("/paiement", name="paiement-billetterie")
     */
    public function paiementAction(Request $request)
    {
        if($this->get('session')->get("etapeValidee") != 'infos-visiteurs' && $this->get('session')->get("etapeValidee") != 'paiement') {
            throw new \Exception('Vous ne pouvez pas accéder à cette page.');
        }
        if($this->get('session')->has('commande') === false) {
            return $this->redirectToRoute('session-expiree-billetterie');
        }
        //indique que l'etape est validée
        $this->get('session')->set('etapeValidee', 'paiement');
        // affiche la page paiement.html.twig
        return $this->render('billetterie/paiement.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @Route(
     *     "/retour-paiement",
     *     name="retour-paiement-billetterie",
     *     methods="POST"
     * )
     */
    public function retourPaiementAction(Request $request)
    {
        if($this->get('session')->get("etapeValidee") != 'paiement') {
            throw new \Exception('Vous ne pouvez pas accéder à cette page.');
        }
        if($this->get('session')->has('commande') === false) {
            return $this->redirectToRoute('session-expiree-billetterie');
        }
        $commande = $this->get('session')->get('commande');
        // recupère le token de la transaction
        $token = $request->get('stripeToken');
        // Crée la facturation et redirige vers la page de paiement en cas d'erreur
        if($this->get('app.createurfacturationstripe')->creerFacturation($token) === false) {
            return $this->redirectToRoute("paiement-billetterie");
        }
        // traitement sur la commande : ajout du code de reservation et de la date de reservation
        $this->get('app.gestionnairecommande')->traiterCommandePageRetourPaiement($this->get('session')->get('commande'), $token);
        // vérifie si l'email est envoyé (permet d'eviter le renvoi du mail en cas de retour arrière)
        if($this->get('app.mailer')->envoyerMailCommande($commande)) {
            $this->get('app.gestionnairecommande')->enregistrerCommande($commande);
        }
        //indique que l'etape est validée
        $this->get('session')->set('etapeValidee', 'retour-paiement');
        // redirige sur la page confirmation de paiement
        return $this->redirectToRoute("confirmation-paiement-billetterie");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @internal param \Swift_Mailer $mailer
     * @internal param Request $request
     * @Route("/confirmation-paiement", name="confirmation-paiement-billetterie")
     */
    public function confirmationPaiementAction()
    {
        if($this->get('session')->get("etapeValidee") != 'retour-paiement') {
            throw new \Exception('Vous ne pouvez pas accéder à cette page.');
        }
        if($this->get('session')->has('commande')){
            $codeReservation = $this->get('session')->get('commande')->getCodeReservation();
            $this->get('session')->set('codeReservation', $codeReservation);
        } elseif($this->get('session')->has('codeReservation')) {
            $codeReservation = $this->get('session')->get('codeReservation');
        } else {
            throw new \Exception('Code de réservation inexistant : contacter nous si vous ne recevez pas l\'email de confirmation de commande.');
        }

        $this->get('session')->remove('commande');
        // affiche la page confirmationPaiement.html.twig
        return $this->render('billetterie/confirmationPaiement.html.twig', array('codeReservation' => $codeReservation));
    }

    /**
     * @param $codeReservation
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @Route("/email/{codeReservation}", name="affichage-email")
     */
    public function affichageEmailAction($codeReservation)
    {
        $commande = $this->getDoctrine()->getManager()->getRepository('AppBundle:Commande')->findByCodeReservation($codeReservation);
        if($commande === false) {
            throw new \Exception("Code de reservation incorrect ou inexistant");
        }
        //affiche l'email
        return $this->render('billetterie/Emails/confirmationCommande.html.twig', array('commande' => $commande));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/session-expiree", name="session-expiree-billetterie")
     */
    public function sessionExpireeAction() {
        return $this->render('billetterie/sessionExpiree.html.twig');
    }
}
