<?php

namespace Caserne\PompierBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

const fieldNames = array('idGarde','idPompier', 'dispo', 'golor');


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
     *  @ORM\OneToOne(targetEntity="Pompier", inversedBy="calendrier")
     *  @ORM\JoinColumn(name="idPompier", referencedColumnName="id")
     */
    private $idPompier;

    /**
     * @var string
     * @ORM\Column(name="dispo", type="string", length=255)
     */
    private $dispo;

    private $color;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"dispo"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

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

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }
}

