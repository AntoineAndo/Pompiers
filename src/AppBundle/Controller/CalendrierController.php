<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Calendrier;
use AppBundle\Form\CalendrierType;

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

        $entities = $em->getRepository('AppBundle:Calendrier')->findAll();

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
     * Finds and displays a Calendrier entity.
     *
     * @Route("/{id}", name="calendrier_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Calendrier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Calendrier entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

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
