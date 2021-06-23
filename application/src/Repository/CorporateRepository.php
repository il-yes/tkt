<?php

namespace App\Repository;

use App\Entity\Corporate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function findPaginatedCorporates($page, int $limit): array
    {
        $query = $this->createQueryBuilder('c')
                    ->from(Corporate::class, 'Corporate')
                    ->orderBy(
                        'Corporate.name', 
                        $this->order !== null ? $this->order : 'ASC'
                    );

        if(!$this->hasFilter) {
            $query->setFirstResult(($page * $limit) - $limit);
        }           
        $query->setMaxResults($limit);

        if ($this->name !== null) {
            $query
                ->andWhere('c.name = :name')
                ->setParameter('name', $this->name);
        }

        $this->hasFilter = false;
        return $query->getQuery()->getResult();
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
}
