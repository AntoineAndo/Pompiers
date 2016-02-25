<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Pompier;
use AppBundle\Form\PompierType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Pompier controller.
 *
 * @Route("/pompier")
 */
class PompierController extends Controller
{

    /**
     * Lists all Pompier entities.
     *
     * @Route("/", name="pompier")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Pompier')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Pompier entity.
     *
     * @Route("/", name="pompier_create")
     * @Method("POST")
     * @Template("AppBundle:Pompier:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Pompier();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('pompier_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Pompier entity.
     *
     * @param Pompier $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Pompier $entity)
    {
        $form = $this->createForm(new PompierType(), $entity, array(
            'action' => $this->generateUrl('pompier_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Pompier entity.
     *
     * @Route("/new", name="pompier_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Pompier();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Pompier entity.
     *
     * @Route("/{id}", name="pompier_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Pompier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pompier entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Pompier entity.
     *
     * @Route("/{id}/edit", name="pompier_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Pompier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pompier entity.');
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
    * Creates a form to edit a Pompier entity.
    *
    * @param Pompier $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Pompier $entity)
    {
        $form = $this->createForm(new PompierType(), $entity, array(
            'action' => $this->generateUrl('pompier_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Pompier entity.
     *
     * @Route("/{id}", name="pompier_update")
     * @Method("PUT")
     * @Template("AppBundle:Pompier:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Pompier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Pompier entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('pompier_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Pompier entity.
     *
     * @Route("/{id}", name="pompier_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Pompier')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Pompier entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('pompier'));
    }

    /**
     * Creates a form to delete a Pompier entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pompier_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
