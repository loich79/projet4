<?php

namespace Tests\AppBundle\Utils;

use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;
use AppBundle\Utils\VerificateurDispoBillets;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;


/**
 * Class VerificateurDispoBilletsTest
 * @package Tests\AppBundle\Utils
 */
class VerificateurDispoBilletsTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }


    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }

    /**
     * test pour vérifier le service de vérification de disponibilité de billets renvoie true quand il y a des
     * billets disponibles pour la date selectionnée
     */
    public function testDispoBilletsOK()
    {
        // cree un mock la session
        $session = new Session(new MockFileSessionStorage());
        // crée le vérificateur de dispo des billets
        $verificateurDispo = new VerificateurDispoBillets($this->em, $session);
        // teste si il y a bien de la dispo pour les billets pour aujourd'hui
        $this->assertTrue( $verificateurDispo->verifierDispoBillets(new \DateTime()));
    }

    /**
     * test pour vérifier le service de vérification de disponibilité de billets renvoie false quand il n'y a pas de
     * billets disponibles pour la date selectionnée
     */
    public function testDispoBilletsKO()
    {
        // cree un mock la session
        $session = new Session(new MockFileSessionStorage());

        // crée une commande avec 1001 billets et la sauvegarde en bdd
        $commande = new Commande();
        $commande
            ->setDateVisite(new \DateTime("2019-09-21"))
            ->setType('journee')
            ->setMontantTotal(16016)
            ->setNombreBillets(1001)
            ->setDateReservation(new \DateTime())
            ->setCodeReservation('azertyuiop')
            ->setEmail('test@gmail.com')
            ->setEmailSent(true)
        ;
        for ($i = 0; $i<1001; $i++) {
           $billet = new Billet();
           $billet
               ->setTarifReduit(false)
               ->setDateNaissance(new \DateTime("1984-12-25"))
               ->setTarif('16')
               ->setNom('Michel')
               ->setPrenom('Michel')
               ->setPays('FR')
           ;
           $commande->addBillet($billet);
        }
        $this->em->persist($commande);
        $this->em->flush();

        // crée le vérificateur de dispo des billets
        $verificateurDispo = new VerificateurDispoBillets($this->em, $session);
        // teste si il n'y a pas de dispo pour les billets pour la date de la visite
        $this->assertFalse($verificateurDispo->verifierDispoBillets($commande->getDateVisite()));
    }
}
