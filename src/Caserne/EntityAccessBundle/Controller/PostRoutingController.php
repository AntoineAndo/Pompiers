<?php

namespace Caserne\EntityAccessBundle\Controller;

use Caserne\EntityAccessBundle\ResourceSelector\UrlResourceSelector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class PostRoutingController extends Controller
{

    /**
     * @param Request $request
     * @Route("/{url}", requirements={"url" = ".*\/"})
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeTrailingSlashAction(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

        return $this->redirect($url, 301);
    }

    /**
     * @Route("/{entity}/new", name="caserne_entityaccess_postrouteur_new", defaults={"action"="new", "slug"=null})
     * @Route("/{entity}/search", name="caserne_entityaccess_postrouteur_search", defaults={"action"="search", "slug"=null})
     * @Route("/{entity}/{slug}/{action}", name="caserne_entityaccess_postrouteur_index", defaults={"slug"=null, "action"=null})
     * @Route("/{entity}/{action}", name="caserne_entityaccess_postrouteur_action", defaults={"action"="null", "slug"=null})
     */
    public function reRouteAction($entity, $slug, $action, Request $request)
    {
        $method = $request->getMethod();

        $urlResourceSelector = new UrlResourceSelector();

        $controllerClass = $urlResourceSelector->getControllerClass($entity, $this->getDoctrine());

        if (!$controllerClass) {
            return $this->forward("Caserne\\EntityAccessBundle\\Controller\\DefaultController::notFoundAction", array("entityShortName" => "Entity")
            );
        }

        $fqn = $controllerClass['fqn'];

        $controllerClass = $controllerClass['controller'];

        $action = $urlResourceSelector->getActionByUrl($method, $action, $slug);

        $request = $request->duplicate(array('fqn' => $fqn));

        $entityConfig = $this->get('entity_access.config');
        $entityConfig->setEntityClass($fqn);
        $entityConfig->setEntityShortName($entity);

        if (!$slug) {
            return $this->forward($controllerClass.'::'.$action, array(
                'request' => $request,
            ));
        }

        return $this->forward($controllerClass.'::'.$action, array(
            'slug' => $slug,
            'request' => $request,
        ));
    }
}
