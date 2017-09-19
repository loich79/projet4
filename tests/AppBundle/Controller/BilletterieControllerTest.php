<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Commande;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class BilletterieControllerTest
 * @package Tests\AppBundle\Controller
 */
class BilletterieControllerTest extends WebTestCase
{

    /**
     * test la route "/"
     */
    public function testRouteIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Horaires', $crawler->filter('#content h2')->text());
    }

    /**
     * test la route "/choix-visite"
     */
    public function testRouteChoixVisite()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $session = new Session(new MockFileSessionStorage());
        $container->set('session', $session);

        $commande = new Commande();
        $session->set('commande', $commande);

        $crawler = $client->request('GET', '/choix-visite');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Votre Visite', $crawler->filter('#content h2')->text());
    }

    /**
     * test la route "/infos-visiteurs"
     */
    public function testRouteInfosVisiteurs()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $session = new Session(new MockFileSessionStorage());
        $container->set('session', $session);

        $commande = new Commande();
        $session->set('commande', $commande);
        $session->set('etapeValidee', 'choix-visite');

        $crawler = $client->request('GET', '/infos-visiteurs');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Informations Visiteurs', $crawler->filter('#content h2')->text());
    }

    /**
     * test la route "/paiement"
     */
    public function testRoutePaiement()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $session = new Session(new MockFileSessionStorage());
        $container->set('session', $session);

        $commande = new Commande();
        $session->set('commande', $commande);
        $session->set('etapeValidee', 'infos-visiteurs');

        $crawler = $client->request('GET', '/paiement');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Récapitulatif et Paiement', $crawler->filter('#content h2')->text());
    }

    /**
     * test la route "/confirmation-paiement"
     */
    public function testRouteConfirmationPaiement()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $session = new Session(new MockFileSessionStorage());
        $container->set('session', $session);

        $commande = new Commande();
        $commande->setCodeReservation('abcd');
        $session->set('commande', $commande);
        $session->set('etapeValidee', 'retour-paiement');

        $crawler = $client->request('GET', '/confirmation-paiement');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Le musée du Louvre vous remercie pour votre réservation et vous souhaite une bonne visite !', $crawler->filter('#content h2')->text());
    }

    /**
     * test la route "/session-expiree"
     */
    public function testRouteSessionExpiree()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/session-expiree');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('La session a expirée. Veuillez recommencer', $crawler->filter('#content p')->text());
    }
}
