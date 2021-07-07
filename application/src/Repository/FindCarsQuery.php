<?php

namespace App\Repository;

use App\Entity\Car;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FindCollectionQuery;



class FindCarsQuery extends FindCollectionQuery
{
    static $tableName = "Car";

    static $className = Car::class;

    public function __construct(EntityManagerInterface $entityManager)
    {
    	parent::__construct();
        $this->entityManager = $entityManager;
    }
}