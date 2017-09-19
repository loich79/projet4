<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadMilleBillets
 * @package AppBundle\DataFixtures\ORM
 */
class LoadMilleBillets extends Fixture
{

    /**
     * charge un data fixture qui permet d'avoir 1001 billets pour la date du 30/11/2018
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $commande = new Commande();
        $commande
            ->setDateVisite(new \DateTime("2018-11-30"))
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
        $manager->persist($commande);
        $manager->flush();
    }
}
