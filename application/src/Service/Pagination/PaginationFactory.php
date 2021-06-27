<?php

namespace App\Service\Pagination;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Pagination\PaginatedCollection;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Repository\CorporateRepository;


class PaginationFactory
{
	private $router;

    private $corporateRepository;

    public function __construct(
    	RouterInterface $router, CorporateRepository $repository
    )
    {
        $this->router = $router;
        $this->corporateRepository = $repository;
    }

    public function createCollection(
    	Paginator $qb, Request $request, $route, array $routeParams = array()
    ): PaginatedCollection
    {
        $page = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', CorporateRepository::LIMIT);
    	
        $paginatedCollection = new PaginatedCollection($qb, $page, $limit);

         // make sure query parameters are included in pagination links
        $routeParams = array_merge($routeParams, $request->query->all());

        $createLinkUrl = function($targetPage) use ($route, $routeParams) {
            return $this->router->generate($route, array_merge(
                $routeParams,
                array('page' => $targetPage)
            ));
        };

        $paginatedCollection->addLink('self', $createLinkUrl($page));
        $paginatedCollection->addLink('first', $createLinkUrl(1));
        $paginatedCollection->addLink('last', $createLinkUrl(ceil(count($qb) / $limit)));

        if ($paginatedCollection->hasNextPage()) {
            $paginatedCollection->addLink('next', $createLinkUrl($page + 1));
        }
        if ($paginatedCollection->hasPreviousPage()) {
            $paginatedCollection->addLink('prev', $createLinkUrl($page - 1));
        }

        return $paginatedCollection;
    }
}