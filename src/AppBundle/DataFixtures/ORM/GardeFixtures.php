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
use AppBundle\Entity\Garde;

class GardeFixtures implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $garde = new Garde();
        $garde->setDate(new \DateTime());
        $garde->setHoraire('jour');


        $manager->persist($garde);

        $manager->flush();
    }
}