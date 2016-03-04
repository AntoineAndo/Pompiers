<?php
/**
 * Created by PhpStorm.
 * User: antoine
 * Date: 25/11/15
 * Time: 10:50
 */

namespace Caserne\DgServerBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Caserne\UserBundle\Entity\User;

class UserFixtures implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@admin.fr');
        $user->setPlainPassword("1234");
        $user->setEnabled(true);

        $manager->persist($user);

        $manager->flush();
    }
}