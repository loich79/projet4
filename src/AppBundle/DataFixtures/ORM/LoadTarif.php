<?php


namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Tarif;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTarif implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tarifsArray = array('Normal' => 16, 'Enfant' => 8, 'Sénior' => 12, 'Réduit' => 10);
        foreach ($tarifsArray as $category => $montant) {
            $tarif = new Tarif();
            $tarif->setCategorie($category);
            $tarif->setMontant($montant);

            $manager->persist($tarif);
        }

        // On déclenche l'enregistrement de tous les tarifs
        $manager->flush();
    }
}