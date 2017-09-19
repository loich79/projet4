<?php


namespace Tests\AppBundle\Controller;


use AppBundle\Entity\Billet;
use AppBundle\Entity\Commande;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TunnelBilletterieTest
 * @package Tests\AppBundle\Controller
 */
class TunnelBilletterieTest extends WebTestCase
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
     * simule un parcours réussi sur le tunnel de commande de la billetterie jusqu'a la page de paiement
     */
    public function testParcoursCorrectJusquAuPaiement()
    {
        $client = static::createClient();

        //page d'accueil
        $crawler = $client->request('GET', '/');

        $link = $crawler->selectLink('Achetez vos billets')->link();
        $crawler = $client->click($link);

        //page choixVisite
        $form = $crawler->selectButton('Suivant')->form();
        $form['commande_premiere_page[dateVisite]'] = '2019-07-03';
        $form['commande_premiere_page[type]'] = 'demi-journee';
        $form['commande_premiere_page[nombreBillets]'] = 2;
        $client->submit($form);

        $crawler = $client->followRedirect();

        //page infosVisiteurs
        $form = $crawler->selectButton('Paiement')->form();
        $form['commande_deuxieme_page[email][first]'] = 'michel.michel@gmail.com';
        $form['commande_deuxieme_page[email][second]'] = 'michel.michel@gmail.com';
        // 1er billet
        $form['commande_deuxieme_page[billets][0][nom]'] = 'Michel';
        $form['commande_deuxieme_page[billets][0][prenom]'] = 'Michel';
        $form['commande_deuxieme_page[billets][0][dateNaissance][year]'] = '1986';
        $form['commande_deuxieme_page[billets][0][dateNaissance][month]'] = '3';
        $form['commande_deuxieme_page[billets][0][dateNaissance][day]'] = '18';
        $form['commande_deuxieme_page[billets][0][pays]'] = 'FR';
        //2eme billet
        $form['commande_deuxieme_page[billets][1][nom]'] = 'Michel';
        $form['commande_deuxieme_page[billets][1][prenom]'] = 'Michelle';
        $form['commande_deuxieme_page[billets][1][dateNaissance][year]'] = '1943';
        $form['commande_deuxieme_page[billets][1][dateNaissance][month]'] = '8';
        $form['commande_deuxieme_page[billets][1][dateNaissance][day]'] = '21';
        $form['commande_deuxieme_page[billets][1][pays]'] = 'FR';
        $client->submit($form);

        $crawler = $client->followRedirect();

        // test du montant sur la page paiement
        $this->assertContains('Montant total : 28 €', $crawler->filter('#paiement h4')->text());
    }

    /**
     * simule un parcours sur le tunnel de commande de la billetterie dont la date de visite choisie par le visiteur
     * ne peut pas etre sélectionnéecar tous les billets ont été vendus pour cette date
     */
    public function testParcoursIncorrectTousLesBilletsVendus()
    {
        // creation de 1001 billets
        $commande = new Commande();
        $commande
            ->setDateVisite(new \DateTime("2020-09-21"))
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

        // creer le client
        $client = static::createClient();

        //page d'accueil
        //recupère dans le crawler la requete pour l'URI "/"
        $crawler = $client->request('GET', '/');

        $link = $crawler->selectLink('Achetez vos billets')->link();
        $crawler = $client->click($link);

        //page choixVisite
        $form = $crawler->selectButton('Suivant')->form();
        $form['commande_premiere_page[dateVisite]'] = "2020-09-21";
        $form['commande_premiere_page[type]'] = 'demi-journee';
        $form['commande_premiere_page[nombreBillets]'] = 2;
        $crawler = $client->submit($form);

        $this->assertContains('Tous les billets pour cette date ont été vendus. Veuillez selectionner une autre date !', $crawler->filter('.alert-danger')->text());


    }

}
