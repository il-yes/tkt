<?php

namespace App\Controller;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Pagination\PaginatedCollection;
use App\Service\Pagination\PaginationFactory;
use App\Repository\FindCarsQuery;
use App\Repository\CarRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class CarController extends BaseController
{
    private $carRepository;
    private $paginationFactory;
    private $findCarsQuery;

	public function __construct(
        CarRepository $repository, 
        SerializerInterface $serializer,
        PaginationFactory $paginationFactory,
        FindCarsQuery $findCarsQuery
    )
    {
        $this->carRepository = $repository;
        $this->serializer = $serializer;
        $this->paginationFactory = $paginationFactory;
        $this->findCarsQuery = $findCarsQuery;
    }

    /**
     * @Route("/api/cars", name="cars_list")
     */
    public function index(Request $request): Response
    {
    	$range  = \json_decode($request->query->get('range'));

        $this
        	->findCarsQuery
            ->init(
             	$range, 
             	\json_decode($request->query->get('sort')), 
             	\json_decode($request->query->get('page')),
             	\json_decode($request->query->get('perPage')), 
             	(array) \json_decode($request->query->get('filter'))
            );

		$qb = $this->findCarsQuery->execute();

        $paginatedCollection = $this
                                ->paginationFactory
                                ->createCollection(
                                	$qb, 
                                	$request, 
                                	'cars_list', 
                                	$this->findCarsQuery->getPage(),
                                	$this->findCarsQuery->getPerPage()
                                );

        return $this->createApiResponseForReactAdmin(
        	$this->findCarsQuery->getRange()[0], $paginatedCollection, 'cars'
        );  
    }



}
