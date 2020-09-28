<?php


namespace App\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

abstract class AbstractRepository extends ServiceEntityRepository
{
    protected function paginate(QueryBuilder $qb, $limit = 10, $offset = 0, $page = 0)
    {
        $limit = (int) $limit;
        if(0 == $limit){
            throw new \LogicException('$limit doit etre superieur à 0');
        }

        $pager = new Pagerfanta(new QueryAdapter($qb));
        $currentPage = ceil(($offset + 1) / $limit);
        if($page > 1){
            $pager->setCurrentPage($page);
            $pager->setMaxPerPage((int)$limit);
        }else {
            $pager->setCurrentPage($currentPage);
            $pager->setMaxPerPage((int)$limit);

        }

        return $pager;
    }



}