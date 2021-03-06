<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Garde;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Calendrier;
use AppBundle\Form\CalendrierType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Calendrier controller.
 *
 * @Route("/calendrier")
 */
class CalendrierController extends Controller
{
    /**
     * Lists all Calendrier entities.
     *
     * @Route("/", name="calendrier")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $entities = $em->getRepository('AppBundle:Pompier')->findAll();


        return array(
            'entities' => $entities,
        );
    }

    /**
     * Lists all Calendrier entities.
     *
     * @Route("/valid", name="calendrier_valid")
     * @Method("GET")
     * @Template("AppBundle:Calendrier:valid.html.twig")
     */
    public function validAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare("SELECT calendrier.id, pompier.nom, pompier.prenom, garde.date, garde.horaire FROM calendrier JOIN garde ON idGarde = garde.id JOIN pompier ON idPompier = pompier.id  WHERE valide = :valide");
        $statement->bindValue('valide', 0);
        $statement->execute();
        $calendrier = $statement->fetchAll();

        dump($calendrier);


        return array(
            'calendrier' => $calendrier
        );
    }



    /**
     * Creates a new Calendrier entity.
     *
     * @Route("/", name="calendrier_create")
     * @Method("POST")
     * @Template("AppBundle:Calendrier:new.html.twig")
     */
    public function createAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        if ($request->isXMLHttpRequest()) {
            $idPompier = $request->request->get("pompier");
            $horaire = $request->request->get("horaire");
            $date = $request->request->get("date");
            $date = date_create_from_format('m/j/Y', $date);
            $dispo = $request->request->get("dispo");

            $garde = $em->getRepository('AppBundle:Garde')->findOneByDate($date);
            $pompier = $em->getRepository('AppBundle:Pompier')->find($idPompier);

            if(!$garde || ($garde->getHoraire() != $horaire)){
                $garde = new Garde();
                $garde->setDate($date);
                $garde->setHoraire($horaire);
                $em->persist($garde);
                $em->flush();
            }

            $calendrier = new Calendrier();
            $calendrier->setIdGarde($garde);
            $calendrier->setIdPompier($pompier);
            $calendrier->setDispo($dispo);
            $em->persist($calendrier);
            $em->flush();

            $response = array(
                "title" => $dispo . " - " . $horaire,
            );


            $date = $date->format('Y-m-d');

            $response['id'] = $calendrier->getId();
            //$response['url'] = "http://www.google.fr";

            if($horaire == 'nuit') {
                $start = "19:00";
                $end = "20:00";
                $creneau = "Nuit";
            }
            else {
                $start = "07:00";
                $end = "19:00";
                $creneau = "Jour";
            }

            $response["title"] = $dispo . " - " . $creneau;

            $response["start"] = $date . " " . $start;
            $response["end"] = $date . " " . $end;

            $response["className"] = array("id-".$calendrier->getId());

            if($dispo == "Garde")
                $response["backgroundColor"] = "green";
            elseif ($dispo == "Astreinte")
                $response["backgroundColor"] = "orange";
            elseif ($dispo == "Urgence")
                $response["backgroundColor"] = "red";
            else
                $response["backgroundColor"] = "#303030";


            $response = json_encode($response, JSON_PRETTY_PRINT);

            return new JsonResponse($response);

        }


        $entity = new Calendrier();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('calendrier'));
        }

        /*  return array(
              'entity' => $entity,
              'form'  => $form->createView(),
          );*/
    }

    /**
     * Creates a form to create a Calendrier entity.
     *
     * @param Calendrier $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Calendrier $entity)
    {
        $form = $this->createForm(new CalendrierType(), $entity, array(
            'action' => $this->generateUrl('calendrier_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Calendrier entity.
     *
     * @Route("/new", name="calendrier_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Calendrier();
        $form  = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }



    /**
     * @Method("GET")
     * @Route("/{id}/json", name="getJson")
     */
    public function getJSONAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {

            $slug = explode("/",$_SERVER['REQUEST_URI']);
            array_pop($slug);

            $events = array();
            $em = $this->getDoctrine()->getManager();

            $entity = $em->getRepository("AppBundle:Pompier")->find(array_pop($slug));

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Calendrier entity.');
            }

            $connection = $em->getConnection();
            $statement = $connection->prepare("SELECT calendrier.id AS idCalendrier, date, horaire, dispo, valide FROM calendrier JOIN garde ON idGarde = garde.id WHERE idPompier = :id");
            $statement->bindValue('id', $entity->getId());
            $statement->execute();
            $results = $statement->fetchAll();



            foreach($results as $garde){
                $truc = explode("-", $garde["date"]);
                $garde["jour"] = intval(array_pop($truc));
                $garde["mois"] = intval(array_pop($truc));
                $garde["annee"] = intval(array_pop($truc));

                array_push($events, $garde);
            }

            $jours = array();
            foreach($events as $jour){

                $jsonEvent = array();

                $jsonEvent['id'] = $jour['idCalendrier'];
                //$jsonEvent['url'] = "http://www.google.fr";

                if($jour["horaire"] == 'nuit') {
                    $start = "19:00";
                    $end = "20:00";
                    $creneau = "Nuit";
                }
                else {
                    $start = "07:00";
                    $end = "19:00";
                    $creneau = "Jour";
                }

                $jsonEvent["title"] = $jour["dispo"] . " - " . $creneau;

                $jsonEvent["start"] = $jour['date'] . " " . $start;
                $jsonEvent["end"] = $jour['date'] . " " . $end;

                $jsonEvent["className"] = array("id-".$jour['idCalendrier']);
                if($jour['valide'] == 1)
                    array_push($jsonEvent["className"],"valide");

                if($jour['dispo'] == "Garde")
                    $jsonEvent["backgroundColor"] = "green";
                elseif ($jour['dispo'] == "Astreinte")
                    $jsonEvent["backgroundColor"] = "orange";
                elseif ($jour['dispo'] == "Urgence")
                    $jsonEvent["backgroundColor"] = "red";
                else
                    $jsonEvent["backgroundColor"] = "#303030";

                array_push($jours, $jsonEvent);
            }

            $jours = json_encode($jours, JSON_PRETTY_PRINT);




            return new JsonResponse($jours);

            //  return new JsonResponse(array('data' => 'this is a json response'));
        }
        return new Response('This is not ajax!', 400);
    }




    /**
     *
     * @Method("GET")
     * @Route("/{id}", name="calendrier_show")
     * @Template()
     */
    public function showAction(Request $request)
    {
        $slug = explode("/",$_SERVER['REQUEST_URI']);

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository("AppBundle:Pompier")->find(array_pop($slug));


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Calendrier entity.');
        }

        $deleteForm = $this->createDeleteForm($entity->getId());

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Displays a form to edit an existing Calendrier entity.
     *
     * @Route("/{id}/edit", name="calendrier_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Calendrier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Calendrier entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Calendrier entity.
     *
     * @param Calendrier $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Calendrier $entity)
    {
        $form = $this->createForm(new CalendrierType(), $entity, array(
            'action' => $this->generateUrl('calendrier_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Calendrier entity.
     *
     * @Route("/{id}", name="calendrier_update")
     * @Method("PUT")
     * @Template("AppBundle:Calendrier:edit.html.twig")
     */
    public function updateAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();


        if ($request->isXMLHttpRequest()) {
            $truc = $request->request->get("id");

            $connection = $em->getConnection();
            $statement = $connection->prepare("UPDATE calendrier SET valide = 1 WHERE idGarde = :id");
            $statement->bindValue('id', $truc);
            $statement->execute();


            $result = array('data' => $truc);
            return new JsonResponse($result);   
        }


        $em = $this->getDoctrine()->getManager();

        $slug = explode("/",$_SERVER['REQUEST_URI']);

        $entity = $em->getRepository('AppBundle:Calendrier')->find(array_pop($slug));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Calendrier entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);


        $em->flush();


        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Calendrier entity.
     *
     * @Route("/{id}", name="calendrier_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Calendrier')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Calendrier entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('calendrier'));
    }


    /**
     * Deletes a Calendrier entity.
     *
     * @Route("/delete", name="calendrier_delete_post")
     * @Method("POST")
     */
    public function deletePostAction(Request $request)
    {

        if ($request->isXMLHttpRequest()) {
            $id = $request->request->get("id");

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Calendrier')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Calendrier entity.');
            }

            $em->remove($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('calendrier'));
        }
    }


    /**
     * Creates a form to delete a Calendrier entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('calendrier_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }
}
