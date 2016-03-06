<?php

namespace AppBundle\Controller;

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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Pompier')->findAll();

        dump($entities);

        return array(
            'entities' => $entities,
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
        $entity = new Calendrier();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('calendrier_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
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
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }



    /**
     * @Method("GET")
     * @Route("/{slug}/json", name="getJson")
     */
    public function getJSONAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {

            $slug = explode("/",$_SERVER['REQUEST_URI']);
            array_pop($slug);

            $events = array();
            $em = $this->getDoctrine()->getManager();

            $entity = $em->getRepository("AppBundle:Pompier")->findOneBySlug(array_pop($slug));

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Calendrier entity.');
            }

            $connection = $em->getConnection();
            $statement = $connection->prepare("SELECT * FROM calendrier JOIN garde ON idGarde = garde.id WHERE idPompier = :id");
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
                array_push($jours, array("title" => $jour["dispo"]
                                                ,"start" => $jour["date"]));
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
     * @Route("/{slug}", name="calendrier_show")
     * @Template()
     */
    public function showAction($slug)
    {
        $events = array();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository("AppBundle:Pompier")->findOneBySlug($slug);

     //   dump($entity);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Calendrier entity.');
        }

        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT * FROM calendrier JOIN garde ON idGarde = garde.id WHERE idPompier = :id");
        $statement->bindValue('id', $entity->getId());
        $statement->execute();
        $results = $statement->fetchAll();


        dump($results);

        $jour = 0;
        $mois = 0;
        $annee = 0;

        foreach($results as $garde){

            $truc = explode("-", $garde["date"]);
            $garde["jour"] = intval(array_pop($truc));
            $jour = $garde['jour'];
            $garde["mois"] = intval(array_pop($truc));
            $mois = $garde['mois'];
            $garde["annee"] = intval(array_pop($truc));
            $annee = $garde['annee'];
            dump($garde);

            array_push($events, $garde);
        }

        dump($events);

        $jours = new \stdClass();
        $jours->data = array();
        foreach($truc as $jour){
            array_push($jours->data, $jour["date"]);
        }

        $jours = json_encode($jours);
       // dump($jours);

        $deleteForm = $this->createDeleteForm($entity->getId());

        return array(
            'jours'       => $jours,
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
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Calendrier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Calendrier entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('calendrier_edit', array('id' => $id)));
        }

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
