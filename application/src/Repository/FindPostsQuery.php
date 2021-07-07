<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FindCollectionQuery;



class FindPostsQuery extends FindCollectionQuery
{
    static $tableName = "Post";

    static $className = Post::class;

    public function __construct(EntityManagerInterface $entityManager)
    {
    	parent::__construct();
        $this->entityManager = $entityManager;
    }
}