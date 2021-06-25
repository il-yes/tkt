<?php

namespace App\Repository;

use App\Entity\Corporate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Corporate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Corporate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Corporate[]    findAll()
 * @method Corporate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CorporateRepository extends ServiceEntityRepository
{

    public const LIMIT = 10;

    private $hasFilter = false;

    private $name;
    private $order;
    private $filter = [];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Corporate::class);
    }


    public function totalCorporates()
    {
        $query = $this->createQueryBuilder('c')
                    ->select('Count(c)');
        
        return $query->getQuery()->getSingleScalarResult();

    }

    public function findPaginatedCorporates($page, int $limit) : Paginator 
    {
        $query = $this
                    ->createQueryBuilder('c')
                    ->orderBy(
                        'c.id', 
                        $this->order !== null ? $this->order : 'ASC');

        if($this->name !== null) {
            $query
                ->andWhere('c.name = :name')
                ->setParameter('name', $this->name);
        }

        if(count($this->filter) > 0) {
            foreach ($this->filter as $key => $filter) {
                $query
                    ->andWhere($filter['where'])
                    ->setParameter($key, $filter['param']);
            }
        }


        if(!$this->hasFilter) {
            $query->setFirstResult(($page * $limit) - $limit);
        }           
        $query->setMaxResults($limit);

        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $this->hasFilter = false;

        return $paginator;
    }

    public function findAllQueryBuilder()
    {
        return $this->createQueryBuilder('corporate');
    }

    public function byName(string $aName): self
    {
        $this->name = $aName;
        $this->hasFilter = true;
        return $this;
    }

    public function byOrder(string $order): self
    {
        $this->order = $order;
        $this->hasFilter = true;
        return $this;
    }

    public function byFilter($filters): self
    {
        foreach ($filters as $key => $value) {
            $this->filter[$key]['where'] = 'c.'. $key .' = :'. $key;
            $this->filter[$key]['param'] = $value;
        }

        $this->hasFilter = true;
        return $this;
    }
}
