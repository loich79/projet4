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

    /**
     * envoie le mail de confirmation de commande et indique que le mail est envoyé dans la commmande
     * @param Commande $commande
     * @param $expediteur
     * @return bool
     */
    public function envoyerMailCommande(Commande $commande, $expediteur)
    {
        if(!$commande->getEmailSent()) {
            //envoie du mail de confirmation
            $message = (new \Swift_Message('confirmation de commande'))
                ->setFrom($expediteur)
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
