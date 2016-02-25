<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Garde
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Garde
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var \String
     *
     * @ORM\Column(name="horaire", type="string")
     */
    private $horaire;

    /**
     * @var integer
     *
     * @ORM\OneToMany(targetEntity="Calendrier", mappedBy="idGarde", cascade={"persist", "remove", "merge"})
     */
    private $calendriers;

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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Garde
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getCalendriers()
    {
        return $this->calendriers;
    }

    /**
     * @param int $calendriers
     */
    public function setCalendriers($calendriers)
    {
        $this->calendriers = $calendriers;
    }

    public function __toString(){
        $date = '';
        try{
            $dt = $this->getDate();
            $date = $dt->format('d/m/Y');
        }catch (Exception $e){
        }

        return $date;
    }

    /**
     * @return String
     */
    public function getHoraire()
    {
        return $this->horaire;
    }

    /**
     * @param String $horaire
     */
    public function setHoraire($horaire)
    {
        $this->horaire = $horaire;
    }
}

