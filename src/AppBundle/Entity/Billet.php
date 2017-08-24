<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Billet
 *
 * @ORM\Table(name="billet")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BilletRepository")
 */
class Billet
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;


    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=255)
     */
    private $pays;

    /**
     * @var string
     *
     * @ORM\Column(name="date_naissance", type="date")
     */
    private $dateNaissance;

    /**
     * @var int
     *
     * @ORM\Column(name="tarif", type="integer")
     */
    private $tarif;

    /**
     * @var boolean
     *
     * @ORM\Column(name="tarif_reduit", type="boolean", options={"default":false} )
     */
    private $tarifReduit;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Commande", inversedBy="billets", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $commande;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Billet
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Billet
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set tarif
     *
     * @param int $tarif
     *
     * @return Billet
     */
    public function setTarif(int $tarif = null)
    {
        $this->tarif = $tarif;

        return $this;
    }

    /**
     * Get tarif
     *
     * @return \int
     */
    public function getTarif()
    {
        return $this->tarif;
    }

    /**
     * Set commande
     *
     * @param \AppBundle\Entity\Commande $commande
     *
     * @return Billet
     */
    public function setCommande(\AppBundle\Entity\Commande $commande)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get commande
     *
     * @return \AppBundle\Entity\Commande
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Billet
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Billet
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set tarifReduit
     *
     * @param boolean $tarifReduit
     *
     * @return Billet
     */
    public function setTarifReduit($tarifReduit)
    {
        $this->tarifReduit = $tarifReduit;

        return $this;
    }

    /**
     * Get tarifReduit
     *
     * @return boolean
     */
    public function getTarifReduit()
    {
        return $this->tarifReduit;
    }
}
