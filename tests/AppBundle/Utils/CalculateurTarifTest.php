<?php
use PHPUnit\Framework\TestCase;
use AppBundle\Entity\Billet;
use AppBundle\Utils\CalculateurTarif;

class CalculateurTarifTest extends TestCase
{
    public function testTarifAdulte()
    {
        $billet1 = new Billet();
        $calculateur = new CalculateurTarif();

        $billet1->setDateNaissance(new DateTime('1985-12-25'));


        $this->assertSame(16, $calculateur->determinerTarif($billet1));
    }
    public function testTarifSenior()
    {
        $billet1 = new Billet();
        $calculateur = new CalculateurTarif();

        $billet1->setDateNaissance(new DateTime('1955-12-25'));


        $this->assertSame(12, $calculateur->determinerTarif($billet1));
    }
    public function testTarifEnfant()
    {
        $billet1 = new Billet();
        $calculateur = new CalculateurTarif();

        $billet1->setDateNaissance(new DateTime('2005-12-25'));


        $this->assertSame(8, $calculateur->determinerTarif($billet1));
    }
    public function testTarifEnfantBasAge()
    {
        $billet1 = new Billet();
        $calculateur = new CalculateurTarif();

        $billet1->setDateNaissance(new DateTime('2015-12-25'));


        $this->assertSame(0, $calculateur->determinerTarif($billet1));
    }
    public function testTarifReduitEnfantBasAge()
    {
        $billet1 = new Billet();
        $calculateur = new CalculateurTarif();

        $billet1->setDateNaissance(new DateTime('2015-12-25'));
        $billet1->setTarifReduit(true);


        $this->assertSame(0, $calculateur->determinerTarif($billet1));
    }
    public function testTarifReduitAdulte()
    {
        $billet1 = new Billet();
        $calculateur = new CalculateurTarif();

        $billet1->setDateNaissance(new DateTime('1985-12-25'));
        $billet1->setTarifReduit(true);


        $this->assertSame(10, $calculateur->determinerTarif($billet1));
    }

}
