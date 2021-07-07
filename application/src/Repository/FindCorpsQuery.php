<?php

namespace App\Repository;

use App\Entity\Corporate;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FindCollectionQuery;



class FindCorpsQuery extends FindCollectionQuery
{
    static $tableName = "Corporate";

    static $className = Corporate::class;

    public function __construct(EntityManagerInterface $entityManager)
    {
    	parent::__construct();
        $this->entityManager = $entityManager;
    }
}