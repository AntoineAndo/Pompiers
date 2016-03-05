<?php

namespace Caserne\PompierBundle\Controller;

use Caserne\EntityAccessBundle\Controller\DefaultController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Caserne\EntityAccessBundle\Utils\GenericRepository;
use Caserne\EntityAccessBundle\Utils\Form;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class CalendrierController extends DefaultController
{
    private $repository;
    private $forms;
    private $fieldsList;
    private $fieldsName;
    private $entityAccessConfiguration;

    /**
     * @Template("EntityAccessBundle:Calendrier:index.html.twig")
     */
    public function indexAction()
    {

        $form = $this->getForms();
        $repo = new GenericRepository();

        $entityConfig = $this->getEntityConfig();

        $entities = $this->getDoctrine()->getRepository('PompierBundle:Pompier')->findAll();

        dump($entities);

        $searchForm = $form->createSearchForm();
        $searchForm->add('submit', 'submit', array('label' => 'Search'));

        if(defined("Caserne\\PompierBundle\\Entity\\Pompier::fieldNames"))
            $fieldsName = constant("Caserne\\PompierBundle\\Entity\\Pompier::fieldNames");

        /*



        $entityName = $entityConfig->getEntityShortName();
        //$fieldsName = $entityConfig->getFieldsName();

        dump(constant($entity."::fieldNames"));


        $query = $repo->createQuery($fieldsName, $repository);

        $entities = $query->getResult();

*/

        return array(
            'entities' => $entities,
            'entityName' => "Calendrier",
            'fieldnames' => $fieldsName,
            'search_form' => $searchForm->createView(),
        );

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        $form = $this->getForms()->createCreateForm("Caserne\\EntityAccessBundle\\Form\\CalendrierType");
        $entity = $form['entity'];
        $form = $form['form'];

        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl(
            'caserne_entityaccess_postrouteur_action',
            array(
                'entity' => $this->getEntityConfig()->getEntityShortName(),
                'slug' => $entity->getSlug()
            )
        ));
    }

    /**
     * @Template("EntityAccessBundle:Default:new.html.twig")
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction()
    {
        dump("ok");
        $form = $this->getForms()->createCreateForm("Caserne\\EntityAccessBundle\\Form\\CalendrierType");
        $form = $form['form'];

        $form->add('submit', 'submit', array('label' => 'Create'));

        return array(
            'entityName' => $this->getEntityConfig()->getEntityShortName(),
            'form' => $form->createView(),
        );
    }

    /**
     * @Template()
     * @param $slug
     * @return array
     */
    public function editAction($slug)
    {
        $form = $this->getForms();
        $deleteForm = $form->createDeleteForm($slug);

        $editForm = $form->createEditForm($slug, $this->getRepository());
        $editForm = $editForm['form'];

        return array(
            'entityName' => $this->getEntityConfig()->getEntityShortName(),
            'slug' => $slug,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @param $slug
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateAction($slug, Request $request)
    {
        $form = $this->getForms()->createEditForm($slug, $this->getRepository());
        $form = $form['form'];

        $entity = $this->getRepository()->findOneBySlug($slug);

        $form->handleRequest($request);
        $this->getDoctrine()->getManager()->flush();

        $slug = $entity->getSlug();

        return $this->redirect($this->generateUrl('caserne_entityaccess_postrouteur_index', array('entity' => $this->getEntityConfig()->getEntityShortName(),
            'slug' => $slug)));
    }

    /**
     * @Method("DELETE")
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $entityName = $this->getEntityConfig()->getEntityShortName();
        $entity = $this->getRepository()->findBySlug($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ' . $entityName . ' entity.');
        }
        $em->remove($entity[0]);
        $em->flush();

        return $this->redirect($this->generateUrl(
            'caserne_entityaccess_postrouteur_index',
            array('entity' => $entityName)
        ));
    }


    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository()
    {
        if (!$this->repository) {
            $this->repository = $this->getDoctrine()->getManager()->getRepository(
                $this->getEntityConfig()->getEntityClass()
            );
        }
        return $this->repository;
    }

    /**
     * @return \Caserne\EntityAccessBundle\Utils\EntityAccessConfig
     */
    protected function getEntityConfig()
    {
        if ($this->entityAccessConfiguration === null) {
            $this->entityAccessConfiguration = $this->get('entity_access.config');
        }
        return $this->entityAccessConfiguration;
    }

    /**
     * @return array
     */
    protected function getFieldsList()
    {
        if (!$this->fieldsList) {
            $this->fieldsList = $this->getEntityConfig()->getFields();
        }
        return $this->fieldsList;
    }

    /**
     * @return Form
     */
    protected function getForms()
    {
        if (!$this->forms) {
            $forms = $this->get('form');
            $forms->setFormFieldList($this->getFieldsList());
            $forms->setFormContainer($this->container);
            $forms->setFormEntityConfig($this->getEntityConfig());
            $this->forms = $forms;
        }
        return $this->forms;
    }

    /**
     * @return array
     */
    protected function getFieldsName()
    {
        if (!$this->fieldsName) {
            $this->fieldsName = $this->getEntityConfig()->getFieldsName();
        }
        return $this->fieldsName;
    }


}
