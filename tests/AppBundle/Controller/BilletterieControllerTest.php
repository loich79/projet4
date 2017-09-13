<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Commande;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BilletterieControllerTest extends WebTestCase
{
    public function testRouteIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Horaires', $crawler->filter('#content h2')->text());
    }
}
