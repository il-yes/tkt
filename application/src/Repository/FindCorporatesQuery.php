<?php

namespace App\Repository;

use App\Entity\Corporate;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */
class FindCorporatesQuery 
{
    public const LIMIT = 10;
    private const TABLE_NAME = "Corporate";

/**
     * @var EntityManagerInterface
     */
    private $entityManager;

    private $hasFilter = false;

    private $name;
    private $order;
    private $orderBy;
    private $filter = [];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function initializeParams(Request $request, $order, $filter)
    {
        if($request->query->get('name') !== null) {
            $this->byName($request->query->get('name'));
        }
        if($request->query->get('order') !== null) {
            $this->byOrder($order);
        }
        if($request->query->get('filter') !== null) {
            $this->byFilter($filter);
        }
        if($request->query->get('order_by') !== null) {
            $this->byOrderBy(
            	(array) \json_decode($request->query->get('order_by'))
            );
        }
    }

    public function execute($page, int $limit) : Paginator 
    {
        $query = $this
        			->entityManager
                    ->createQueryBuilder()
                    ->select(self::TABLE_NAME)
                    ->from(Corporate::class, self::TABLE_NAME)
                    ->orderBy(
                        'Corporate.id', 
                        $this->order !== null ? $this->order : 'DESC');

        if($this->orderBy !== null) {
        	$query->addOrderBy(
                        $this->orderBy['sort'], $this->orderBy['order']
                    );
        }

        if($this->name !== null) {
            $query
                ->andWhere('Corporate.name = :name')
                ->setParameter('name', $this->name);
        }

        if(count($this->filter) > 0) {
            foreach ($this->filter as $key => $filter) {
                $query
                    ->andWhere($filter['where'])
                    ->setParameter($key, $filter['param']);
            }
        }
      
        $query->setFirstResult(($page * $limit) - $limit);
        $query->setMaxResults($limit);

        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $this->hasFilter = false;

        return $paginator;
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
            $this->filter[$key]['where'] = self::TABLE_NAME .'.'. $key .' = :'. $key;
            $this->filter[$key]['param'] = $value;
        }

        $this->hasFilter = true;
        return $this;
    }

    public function byOrderBy($term): self
    {
    	foreach ($term as $key => $value) {
    		$this->orderBy = [
	        	'sort' => self::TABLE_NAME .'.'. strtolower($key),
	        	'order' => $value
	        ];
    	}

        $this->hasFilter = true;
        return $this;
    }
}