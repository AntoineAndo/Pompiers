<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Calendrier
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Calendrier
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Garde", inversedBy="calendriers", cascade={"persist", "merge"})
     * @ORM\JoinColumn(name="idGarde", referencedColumnName="id")
     */
    private $idGarde;

    /**
     *  @ORM\ManyToOne(targetEntity="Pompier", inversedBy="calendrier")
     *  @ORM\JoinColumn(name="idPompier", referencedColumnName="id")
     */
    private $idPompier;

    /**
     * @var string
     * @ORM\Column(name="dispo", type="string", length=255)
     */
    private $dispo;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idGarde
     *
     * @param integer idGarde
     *
     * @return Calendrier
     */
    public function setIdGarde($idGarde)
    {
        $this->idGarde = $idGarde;

        return $this;
    }

    /**
     * Get idDate
     *
     * @return integer
     */
    public function getIdGarde()
    {
        return $this->idGarde;
    }

    /**
     * Set idPompier
     *
     * @param integer $idPompier
     *
     * @return Calendrier
     */
    public function setIdPompier($idPompier)
    {
        $this->idPompier = $idPompier;

        return $this;
    }

    /**
     * Get idPompier
     *
     * @return integer
     */
    public function getIdPompier()
    {
        return $this->idPompier;
    }

    /**
     * Set dispo
     *
     * @param string $dispo
     *
     * @return Calendrier
     */
    public function setDispo($dispo)
    {
        $this->dispo = $dispo;

        return $this;
    }

    /**
     * Get dispo
     *
     * @return string
     */
    public function getDispo()
    {
        return $this->dispo;
    }
}

