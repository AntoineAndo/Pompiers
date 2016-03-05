<?php

namespace Caserne\EntityAccessBundle\Utils;

use Caserne\EntityAccessBundle\Controller\DefaultController;
use Caserne\EntityAccessBundle\Form\GenericType;

class Form extends DefaultController
{
    private $fieldList;
    private $entityConfig;

    /**
     * @param $slug
     * @return \Symfony\Component\Form\Form The form
     *
     * @internal param $entityName
     */
    public function createDeleteForm($slug)
    {
        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'caserne_entityaccess_postrouteur_index',
                    array('entity' => $this->entityConfig->getEntityShortName(), 'slug' => $slug)
                )
            )
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();

        return $form;
    }

    /**
     * @return array
     */
    public function createSearchForm()
    {
        $entityClass = $this->entityConfig->getEntityClass();
        $entity = new $entityClass();

        $array = $this->createForm(new GenericType(), $entity, array(
            'action' => $this->generateUrl(
                'caserne_entityaccess_postrouteur_index',
                array('entity' => $this->entityConfig->getEntityShortName())
            ),
            'required' => false,
            'data_class' => $this->entityConfig->getEntityClass(),
            'method' => 'GET',
            'fields_name' => $this->fieldList,
            'entityName' => $this->entityConfig->getEntityShortName(),
        ));

        return $array;
    }

    /**
     * @return array
     */
    public function createCreateForm($type = "Caserne\\EntityAccessBundle\\Form\\GenericType")
    {
        $entityClass = $this->entityConfig->getEntityClass();
        $entity = new $entityClass();

        return array('form' => $this->createForm(new $type(), $entity, array(
            'action' => $this->generateUrl(
                'caserne_entityaccess_postrouteur_action',
                array('entity' => $this->entityConfig->getEntityShortName())
            ),
            'data_class' => $this->entityConfig->getEntityClass(),
            'method' => 'POST',
            'fields_name' => $this->fieldList,
            'entityName' => $this->entityConfig->getEntityShortName(),
        )), 'entity' => $entity);
    }


    public function createEditForm($slug, $repository, $type = "Caserne\\EntityAccessBundle\\Form\\GenericType")
    {
        $entity = $repository->findOneBySlug($slug);
        $entityName = $this->entityConfig->getEntityShortName();
        $fieldList = $this->fieldList;

        $editForm = $this->createForm(new $type(), $entity, array(
            'action' => $this->generateUrl('caserne_entityaccess_postrouteur_index', array('entity' => $entityName,
                'slug' => $slug,)),
            'data_class' => $this->entityConfig->getEntityClass(),
            'method' => 'PUT',
            'fields_name' => $fieldList,
            'entityName' => $entityName,
            'entity' => $entity
        ));

        $editForm->add('submit', 'submit', array('label' => 'Update'));

        return array(
            'form' => $editForm,
            'entity' => $entity,);
    }

    /**
     * @param $fieldList
     */
    public function setFormFieldList($fieldList)
    {
        $this->fieldList = $fieldList;
    }

    /**
     * @param $container
     */
    public function setFormContainer($container)
    {
        $this->setContainer($container);
    }

    /**
     * @param $entityConfig
     */
    public function setFormEntityConfig($entityConfig)
    {
        $this->entityConfig = $entityConfig;
    }

}