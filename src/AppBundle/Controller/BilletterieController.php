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
        } else {
            $this->get('app.gestionnairecommande')->initialiserCommandePageChoixVisite($commande);
            // test la methode de request et les contenus des champs du formulaire et la dispo des billets pour la date selectionnée
            if ($request->isMethod('POST') && $formCommande->handleRequest($request)->isValid()
                && $this->get('app.verificateurdispobillets')->verifierDispoBillets($commande->getDateVisite())
            ) {
                // traitement sur la commande : correction automatique du type et ajout de la commande en session
                $this->get('app.gestionnairecommande')->traiterCommandePageChoixVisite($commande);
                //indique que l'etape est validé
                $this->get('session')->set('etapeValidee', 'choix-visite');
                // redirige vers la seconde page du tunnel
                return $this->redirectToRoute('infos-visiteurs-billetterie');
            }

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
    public function infosVisiteursAction(Request $request)
    {
        if($this->get('session')->get("etapeValidee") != 'choix-visite') {
            throw new \Exception('Vous ne pouvez pas accéder à cette page.');
        }
        if($this->get('session')->has('commande') === false) {
            return $this->redirectToRoute('session-expiree-billetterie');
        } else {
            // récupère la commande depuis la session
            $commande = $this->get('session')->get('commande');
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
                //indique que l'etape est validé
                $this->get('session')->set('etapeValidee', 'infos-visiteurs');
                // redirige vers la page de paiement
                return $this->redirectToRoute('paiement-billetterie');
            }
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
        if($this->get('session')->get("etapeValidee") != 'infos-visiteurs') {
            throw new \Exception('Vous ne pouvez pas accéder à cette page.');
        }
        if($this->get('session')->has('commande') === false) {
            return $this->redirectToRoute('session-expiree-billetterie');
        }
        //indique que l'etape est validé
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
    public function retourPaiementAction(Request $request, \Swift_Mailer $mailer)
    {
        if($this->get('session')->get("etapeValidee") != 'paiement') {
            throw new \Exception('Vous ne pouvez pas accéder à cette page.');
        }
        if($this->get('session')->has('commande') === false) {
            return $this->redirectToRoute('session-expiree-billetterie');
        }
        $commande = $this->get('session')->get('commande');
        // renseigne la clé secrète de stripe
        Stripe::setApiKey("sk_test_z8jlJCKrSKN0U59keBnd272U");

        // recupère le token de la transaction
        $token = $request->get('stripeToken');
        // Crée la facturation
        try {
            $charge = Charge::create(array(
                "amount" => ($this->get('session')->get('commande')->getMontantTotal()*100), // Amount in cents
                "currency" => "eur",
                "source" => $token,
                "description" => "Paiement Billetterie du Louvre"
            ));
            // traitement sur la commande : ajout du code de reservation et de la date de reservation
            $this->get('app.gestionnairecommande')->traiterCommandePageRetourPaiement($this->get('session')->get('commande'), $token);
            // vérifie si l'email est envoyé (permet d'eviter le renvoi du mail en cas de retour arrière)
            if(!$commande->getEmailSent()) {
                //envoie du mail de confirmation
                $message = (new \Swift_Message('confirmation de commande'))
                    ->setFrom('lhay17@gmail.com')
                    ->setTo($commande->getEmail())
                    ->setBody($this->renderView('Billetterie/Emails/confirmationCommande.html.twig'), 'text/html');
                $mailer->send($message);
                // modifie l'indicateur de mail envoyé
                $commande->setEmailSent(true);
                //enregistre la commande en bdd
                $em = $this->getDoctrine()->getManager();
                $em->persist($commande);
                $em->flush();
            }
            //indique que l'etape est validé
            $this->get('session')->set('etapeValidee', 'retour-paiement');
            // redirige sur la page confirmation de paiement
            return $this->redirectToRoute("confirmation-paiement-billetterie");
        } catch(\Stripe\Error\Card $e) {

            $this->get('session')->getFlashBag()->add("erreur","Une erreur est survenue, veuillez réessayer");
            return $this->redirectToRoute("paiement-billetterie");
            // The card has been declined
        }
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
        // affiche la page confirmationPaiement.html.twig
        return $this->render('billetterie/confirmationPaiement.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/affichage-email", name="affichage-email")
     */
    public function affichageEmailAction()
    {
        //affiche l'email
        return $this->render('billetterie/Emails/confirmationCommande.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/session-expiree", name="session-expiree-billetterie")
     */
    public function sessionExpireeAction() {
        return $this->render('billetterie/sessionExpiree.html.twig');
    }
}