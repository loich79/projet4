<?php
namespace Tests\AppBundle\Utils;

use PHPUnit\Framework\TestCase;
use AppBundle\Entity\Billet;
use AppBundle\Utils\CalculateurTarif;

/**
 * Class CalculateurTarifTest
 * @package Tests\AppBundle\Utils
 */
class CalculateurTarifTest extends TestCase
{
    /**
     * teste le calculateur de tarif pour le tarif normal (compris entre 12 ans et 60 ans
     */
    public function testTarifNormal()
    {
        $billet1 = new Billet();
        $calculateur = new CalculateurTarif();

        $billet1->setDateNaissance(new \DateTime('1985-12-25'));


        $this->assertSame(16, $calculateur->determinerTarif($billet1));
    }

    /**
     * teste le calculateur de tarif pour le tarif sénior ( au delà de 60 ans)
     */
    public function testTarifSenior()
    {
        $billet1 = new Billet();
        $calculateur = new CalculateurTarif();

        $billet1->setDateNaissance(new \DateTime('1955-12-25'));


        $this->assertSame(12, $calculateur->determinerTarif($billet1));
    }

    /**
     * teste le calculateur de tarif pour le tarif enfant (compris entre 4 ans et 12 ans)
     */
    public function testTarifEnfant()
    {
        $billet1 = new Billet();
        $calculateur = new CalculateurTarif();

        $billet1->setDateNaissance(new \DateTime('2005-12-25'));


        $this->assertSame(8, $calculateur->determinerTarif($billet1));
    }

    /**
     * teste le calculateur de tarif pour le tarif enfant de moins de 4 ans
     */
    public function testTarifEnfantBasAge()
    {
        $billet1 = new Billet();
        $calculateur = new CalculateurTarif();

        $billet1->setDateNaissance(new \DateTime('2015-12-25'));


        $this->assertSame(0, $calculateur->determinerTarif($billet1));
    }

    /**
     * teste le calculateur de tarif pour le tarif réduit pour un enfant ( ne doit pas appliquer le tarif réduit)
     */
    public function testTarifReduitEnfantBasAge()
    {
        $billet1 = new Billet();
        $calculateur = new CalculateurTarif();

        $billet1->setDateNaissance(new \DateTime('2015-12-25'));
        $billet1->setTarifReduit(true);


        $this->assertSame(0, $calculateur->determinerTarif($billet1));
    }

    /**
     * teste le calculateur de tarif pour le tarif réduit pour les visiteurs de plus de 12 ans (doit appliquer le tarif réduit)
     */
    public function testTarifReduitAdulte()
    {
        $billet1 = new Billet();
        $calculateur = new CalculateurTarif();

        $billet1->setDateNaissance(new \DateTime('1985-12-25'));
        $billet1->setTarifReduit(true);


        $this->assertSame(10, $calculateur->determinerTarif($billet1));
    }

}
