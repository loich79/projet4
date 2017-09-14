<?php


namespace AppBundle\Utils;


use AppBundle\Entity\Commande;

class Mailer
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function EnvoyerMailCommande(Commande $commande)
    {
        if(!$commande->getEmailSent()) {
            //envoie du mail de confirmation
            $message = (new \Swift_Message('confirmation de commande'))
                ->setFrom('lhay17@gmail.com')
                ->setTo($commande->getEmail())
                ->setBody($this->twig->render('Billetterie/Emails/confirmationCommande.html.twig', array('commande' => $commande)), 'text/html');
            $this->mailer->send($message);
            // modifie l'indicateur de mail envoyé
            $commande->setEmailSent(true);
            return true;
        }
        return false;
    }
}