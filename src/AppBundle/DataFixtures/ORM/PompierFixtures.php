<?php
/**
 * Created by PhpStorm.
 * User: antoine
 * Date: 25/11/15
 * Time: 10:50
 */

namespace AppBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Pompier;

class PompierFixtures implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $pompier = new Pompier();
        $pompier->setNom("Doe");
        $pompier->setPrenom("John");
        $pompier->setSlug("ando-antoine");
        $pompier->setAdresse("2 rue fenelon");

        $manager->persist($pompier);

        $manager->flush();
    }
}