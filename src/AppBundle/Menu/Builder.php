<?php

/**
 * Created by PhpStorm.
 * User: pierre
 * Date: 31/03/15
 * Time: 15:09.
 */
namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $meta = $em->getMetadataFactory()->getAllMetadata();

        $menu = $factory->createItem('root');
        $menu->addChild('Home', ['route' => 'homepage']);

        foreach ($meta as $subMenu) {

            $entityArray = explode("\\", $subMenu->name);

            if ($shortName = $this->excludeEntity($entityArray)) {


                if (!$subMenu->parentClasses) {
                    $menu->addChild(ucfirst($shortName), ['uri' => '/' . $shortName]);
                } else {
                    $parent = $this->getEntityShortName($subMenu->parentClasses[0]);
                    $menu[$parent]->addChild(ucfirst($shortName), ['uri' => '/' . $shortName]);
                }
            }

        }
        return $menu;
    }

    public function excludeEntity($entityArray)
    {

        $entitiesToExclude = array("User", "Contact");

        if (array_shift($entityArray) == "Dawan") {
            $shortName = $this->getEntityShortName($entityArray);
            foreach ($entitiesToExclude as $entityToExclude) {
                if ($shortName == $entityToExclude) {
                    return false;
                }
            }
            return $shortName;
        }
        return false;
    }

    public function getEntityShortName($entity)
    {
        if (!is_array($entity)) {
            $entity = explode("\\", $entity);
        }
        return array_pop($entity);
    }


}