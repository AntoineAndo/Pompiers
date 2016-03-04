<?php
/**
 * Created by PhpStorm.
 * User: pierre
 * Date: 14/09/15
 * Time: 20:31
 */

namespace Dawan\EntityAccessBundle\Utils;


use Dawan\EntityAccessBundle\ResourceSelector\UrlResourceSelector;
use Doctrine\Bundle\DoctrineBundle\Registry;

class EntityAccessConfig
{
    private $entityClass;
    private $entityShortName;
    private $fields;
    private $doctrine;


    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return mixed
     */
    public function getEntityShortName()
    {
        return $this->entityShortName;
    }

    /**
     * @param mixed $entityShortName
     */
    public function setEntityShortName($entityShortName)
    {
        $this->entityShortName = $entityShortName;
    }

    /**
     * @return mixed
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @param mixed $entityClass
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    }

    public function getFields()
    {
        $fieldsToIgnore = array('id', 'slug');
        $fieldArray = array();
        if (!$this->fields) {
            $urlResourceSelector = new UrlResourceSelector();
            $fieldsList = $urlResourceSelector->ignoreFields(
                $this->getMetadata()->getFieldNames(),
                $fieldsToIgnore
            );
            foreach ($fieldsList as $field) {
                $f = $this->getMetadata()->getFieldMapping($field);
                array_push($fieldArray, $f);
            }
            $this->fields = $fieldArray;
        }
        return $this->fields;
    }

    public function getFieldsName()
    {
        $fieldsName = array();
        foreach ($this->getFields() as $field) {
            array_push($fieldsName, $field['fieldName']);
        }
        return $fieldsName;
    }

    private function getMetadata()
    {
        return $this->getDoctrine()->getManager()->getMetadataFactory()->getMetadataFor($this->getEntityClass());
    }

    /**
     * @return mixed
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }

}