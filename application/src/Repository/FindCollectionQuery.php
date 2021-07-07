<?php

namespace App\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */
class FindCollectionQuery 
{
    static $tableName = '';
    static $className = '';

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    protected $hasFilter = false;

    protected $filter = [];

    protected $perPage = 10;
    protected $range;
    protected $sort;
    protected $page;

    public function __construct()
    {
        $this->range = [0, $this->perPage - 1];
        $this->sort = ['id', 'DESC'];
    }

    public function init($range, $sort, $page, $perPage, $filter)
    {
        if($range !== null) {
            $this->range($range);
        }
        if($sort !== null) {
            $this->sort($sort);
        }
        if($page !== null) {
            $this->page($page);
        }
        if($filter !== null) {
            $this->filter($filter);
        }
        if($perPage !== null) {
            $this->perPage($perPage);
        }
    }

    public function execute() : Paginator 
    {
        $query = $this
                    ->entityManager
                    ->createQueryBuilder()
                    ->select(static::$tableName)
                    ->from(static::$className, static::$tableName)
                    ->orderBy(
                        static::$tableName .'.'. $this->sort[0], 
                        $this->sort[1]
                    );

        if(count($this->filter) > 0) {
            foreach ($this->filter as $key => $filter) {
                $query
                    ->andWhere($filter['where'])
                    ->setParameter($key, $filter['param']);
            }
        }

        if($this->page !== null && $this->perPage !== null) {
            $query->setFirstResult(($this->page * $this->perPage) - $this->perPage);
            $query->setMaxResults($this->perPage);
        }else{
            $query->setFirstResult($this->range[0]);
            $query->setMaxResults($this->range[1]);            
        }

        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $this->hasFilter = false;

        return $paginator;
    }

    protected function range(array $range)
    {
        $this->range = $range;
        
        return $this; 
    }

    protected function sort(array $sort) 
    {
        $this->sort = $sort;
        
        return $this;
    }

    protected function perPage($aLimit) 
    {
        $this->perPage = $aLimit;
        
        return $this;
    }

    protected function page($aPage) 
    {
        $this->page = $aPage;
        
        return $this;
    }

    protected function filter(array $aFilter) 
    {
        if($aFilter) {
            foreach ($aFilter as $key => $value) {
                $this->filter[$key]['where'] = static::$className .'.'. strtolower($key) .' = :'. $key;
                $this->filter[$key]['param'] = $value;
            }            
        }

        return $this;
    }

    public function getRange()
    {
        return $this->range;
    }

    public function getPage()
    {
        return \round($this->range[0] / $this->perPage) + 1;
    }

    public function getPerPage()
    {
        return $this->perPage;
    }
}