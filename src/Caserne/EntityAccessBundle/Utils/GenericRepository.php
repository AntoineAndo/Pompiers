<?php
/**
 * Created by PhpStorm.
 * User: antoine
 * Date: 28/09/15
 * Time: 10:11
 */

namespace Dawan\EntityAccessBundle\Utils;


class GenericRepository
{
    public function createQuery($fieldsName, $repository)
    {
        $fields = $this->getSearchFields($_GET, $fieldsName);

        $query = $repository->createQueryBuilder('e');

        if ($fields) {
            $count = 0;
            foreach ($fields as $field) {
                if($count == 0 ) {
                    $query
                        ->where('e.' . key($field) . ' like :' . key($field));
                }
                else {
                    $query
                        ->andWhere('e.' . key($field) . ' like :' . key($field));
                }
                $query->setParameter(key($field), '%' . current($field) . '%');
                $count++;
            }
        }

        $query = $query->getQuery();

        return $query;
    }

    public function getSearchFields($get, $fieldsName)
    {
        $fields = array();
        $getLength = count($get);
        for ($g = 0; $g < $getLength; $g++) {
            if (current($get) == "") {
                unset($get[key($get)]);
            } else {
                for ($f = 0; $f < count($fieldsName); $f++) {
                    if (key($get) == $fieldsName[$f]) {
                        array_push($fields, array(key($get) => current($get)));
                    }
                    next($fieldsName);
                }
                next($get);
            }
        }

        return $fields;
    }

}