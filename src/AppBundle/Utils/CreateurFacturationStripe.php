<?php


namespace AppBundle\Utils;


use Stripe\Charge;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Session\Session;

class CreateurFacturationStripe
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * crée la facturation par Stripe
     * @param $token
     * @return bool
     */
    public function creerFacturation($token)
    {
        // renseigne la clé secrète de stripe
        Stripe::setApiKey("sk_test_z8jlJCKrSKN0U59keBnd272U");
        // Crée la facturation
        try {
            $charge = Charge::create(array(
                "amount" => ($this->session->get('commande')->getMontantTotal()*100),
                "currency" => "eur", "source" => $token, "description" => "Paiement Billetterie du Louvre"));
        } catch(\Stripe\Error\Card $e) {
            $this->session->getFlashBag()->add("erreur","Une erreur est survenue, veuillez réessayer");
            return false;
        }
        return true;
    }
}
