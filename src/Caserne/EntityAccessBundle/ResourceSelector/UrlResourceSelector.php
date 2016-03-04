<?php

namespace Caserne\EntityAccessBundle\ResourceSelector;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class UrlResourceSelector
{
    public function getControllerClass($needle, Doctrine $doctrine)
    {
        $em = $doctrine->getManager();
        $entitiesMetadata = $em->getMetadataFactory()->getAllMetadata();

        foreach ($entitiesMetadata as $meta) {
            if ($meta->isMappedSuperclass) {
                continue;
            }
            $fqn = $meta->getName();
            $fqnFragments = explode('\\', $fqn);
            $className = end($fqnFragments);

            if (strtolower($needle) == strtolower($className)) {
                array_pop($fqnFragments);
                array_pop($fqnFragments);
                $controller = implode('\\', $fqnFragments).'\\Controller\\'.$className.'Controller';
                try {
                    $reflectionController = new \ReflectionClass($controller);
                    if (class_exists($controller) && !$reflectionController->isAbstract()) {
                        return array(
                            'controller' => $controller,
                            'fqn' => $fqn);
                    }
                } catch (\ReflectionException $e) {
                    return array(
                        'controller' => 'Caserne\\EntityAccessBundle\\Controller\\DefaultController',
                        'fqn' => $fqn);
                }
            }
        }
        return null;
    }

    public function getActionByUrl($method, $action, $slug)
    {
        if (!$action && !$slug && $method == 'GET') {
            return 'indexAction';
        }
        if (!$action && !$slug && $method == 'POST') {
            return 'createAction';
        }
        if ($action == 'search' && !$slug) {
            return 'searchAction';
        }
        if ($action == 'new' && !$slug && $method == 'GET') {
            return 'newAction';
        }
        if (!$action && $slug && $method == 'GET') {
            return 'showAction';
        }
        if (!$action && $slug && $method == 'PUT') {
            return 'updateAction';
        }
        if (!$action && $slug && $method == 'DELETE') {
            return 'deleteAction';
        }
        if ($action && $slug && $method == 'GET') {
            return 'editAction';
        }
        if ($action && $slug && $method == 'DELETE') {
            return $action.'Action';
        }
        throw new RouteNotFoundException('No route for '.$method.' '.$action.' '.$slug);
    }

    public function ignoreFields($fieldsName, $fieldsToIgnore)
    {
        for ($i = 0; $i < count($fieldsName); ++$i) {
            for ($j = 0; $j < count($fieldsToIgnore); ++$j) {
                if ($fieldsToIgnore[$j] == $fieldsName[$i]) {
                    unset($fieldsName[$i]);
                    $fieldsName = array_values($fieldsName);
                }
            }
        }

        return $fieldsName;
    }
}
