<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('caserne_homepage', new Route('/hello/{name}', array(
    '_controller' => 'CaserneBundle:Default:index',
)));

return $collection;
