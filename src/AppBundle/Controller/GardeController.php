<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Garde;
use AppBundle\Form\GardeType;

/**
 * Garde controller.
 *
 * @Route("/garde")
 */
class GardeController extends Controller
{

    /**
     * Lists all Garde entities.
     *
     * @Route("/", name="garde")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Garde')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Garde entity.
     *
     * @Route("/", name="garde_create")
     * @Method("POST")
     * @Template("AppBundle:Garde:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Garde();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('garde_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Garde entity.
     *
     * @param Garde $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Garde $entity)
    {
        $form = $this->createForm(new GardeType(), $entity, array(
            'action' => $this->generateUrl('garde_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Garde entity.
     *
     * @Route("/new", name="garde_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Garde();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Garde entity.
     *
     * @Route("/{id}", name="garde_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Garde')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Garde entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Garde entity.
     *
     * @Route("/{id}/edit", name="garde_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Garde')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Garde entity.');
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
    * Creates a form to edit a Garde entity.
    *
    * @param Garde $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Garde $entity)
    {
        $form = $this->createForm(new GardeType(), $entity, array(
            'action' => $this->generateUrl('garde_upgarde', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Upgarde'));

        return $form;
    }
    /**
     * Edits an existing Garde entity.
     *
     * @Route("/{id}", name="garde_upgarde")
     * @Method("PUT")
     * @Template("AppBundle:Garde:edit.html.twig")
     */
    public function upgardeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Garde')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Garde entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('garde_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Garde entity.
     *
     * @Route("/{id}", name="garde_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Garde')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Garde entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('garde'));
    }

    /**
     * Creates a form to delete a Garde entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('garde_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
