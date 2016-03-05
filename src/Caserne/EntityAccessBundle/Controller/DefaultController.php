<?php

namespace Caserne\EntityAccessBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Caserne\EntityAccessBundle\Utils\GenericRepository;
use Caserne\EntityAccessBundle\Utils\Form;


class DefaultController extends Controller
{
    private $repository;
    private $forms;
    private $fieldsList;
    private $fieldsName;
    private $entityAccessConfiguration;

    /**
     * @Template("EntityAccessBundle:Default:index.html.twig")
     */
    public function indexAction()
    {
        dump("ok");

        $form = $this->getForms();
        $repo = new GenericRepository();

        $repository = $this->getDoctrine()->getRepository($this->getEntityConfig()->getEntityClass());

        $entityConfig = $this->getEntityConfig();
        $searchForm = $form->createSearchForm();
        $searchForm->add('submit', 'submit', array('label' => 'Search'));

        $entityName = $entityConfig->getEntityShortName();

        $fieldsName = $entityConfig->getFieldsName();

        $query = $repo->createQuery($fieldsName, $repository);

        $entities = $query->getResult();

        dump($entities);

        return array(
            'entities' => $entities,
            'entityName' => $entityName,
            'fieldnames' => $fieldsName,
            'search_form' => $searchForm->createView(),
        );

    }

    /**
     * @Template()
     * @param $slug
     * @return array
     */
    public function showAction($slug)
    {
        $form = $this->getForms();
        $deleteForm = $form->createDeleteForm($slug);

        $entity = $this->getRepository()->findOneBySlug($slug);
        $entityShortName = $this->getEntityConfig()->getEntityShortName();

        if (!$entity) {
            return $this->forward("Caserne\\EntityAccessBundle\\Controller\\DefaultController::notFoundAction", array(
                'entityShortName' => $entityShortName,
            ));
        }

        $fieldsName = $this->getFieldsName();

        return array(
            'entity' => $entity,
            'entityName' => $entityShortName,
            'fieldsName' => $fieldsName,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        $form = $this->getForms()->createCreateForm();
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
     * @Template()
     */
    public function notFoundAction($entityShortName)
    {
        return array("entityShortName" => $entityShortName);
    }

    /**
     * @Template()
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction()
    {
        $form = $this->getForms()->createCreateForm();
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
