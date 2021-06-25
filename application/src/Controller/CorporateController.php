<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CorporateRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use App\Service\Pagination\PaginatedCollection;
use App\Service\Pagination\PaginationFactory;

class CorporateController extends AbstractController
{
    private $corporateRepository;
    private  $serializer;
    private  $paginationFactory;

    public function __construct(
        CorporateRepository $repository, 
        SerializerInterface $serializer,
        PaginationFactory $paginationFactory
    )
    {
        $this->corporateRepository = $repository;
        $this->serializer = $serializer;
        $this->paginationFactory = $paginationFactory;
    }

    /**
     * @Route("/api/corporates", name="corporate_list")
     */
    public function index(Request $request): Response
    {
        $filter = \json_decode($request->query->get('filter'));
        
        $page = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', CorporateRepository::LIMIT);
        $order = $request->query->get('order', 'asc');

        if ($request->query->get('name') !== null) {
            $this->corporateRepository->byName($request->query->get('name'));
        }
        if ($request->query->get('order') !== null) {
            $this->corporateRepository->byOrder($order);
        }
        if ($request->query->get('filter') !== null) {
            $this->corporateRepository->byFilter($filter);
        }

        $qb = $this->corporateRepository
                    ->findPaginatedCorporates(
                        $page, 
                        $limit
                    );

        $paginatedCollection = $this
                                ->paginationFactory
                                ->createCollection($qb, $request, 'corporate_list');


        //dd($paginatedCollection);

        $response = new Response(
            $this->serializer->serialize([
                'pagination' => [
                    'page' => $paginatedCollection->page(),
                    'pages' => $paginatedCollection->pages(),
                    'limit' => $paginatedCollection->total(),
                    'total' => $paginatedCollection->count(),
                    '_links' => $paginatedCollection->_links()
                ],
                'collection' => $paginatedCollection->items()                
            ], 'json'),
            200, 
            ['Content-Type' => 'application/json']
        );

        return $response;
        

        /* Total collection
        $total = count($response);
        $pagination = [
            'page' => $page,
            'pages' => ceil($total / $limit),
            'total' => $total,
            'limit' => $limit,
            'metaData' => [
                'order' => $order,
                'sector' => $request->query->get('sector', '')
            ]
        ];

        return new Response(
            $this->serializer->serialize([
                'pagination' => $pagination,
                'collection' => $collection
            ], 'json'), 
            200, 
            ['Content-Type' => 'application/json']
        );  */ 

        /* --------- 2eme methode --------------------------------- 
        $qb = $this->corporateRepository->findAllQueryBuilder();
        dd(
            new QueryAdapter()
        );
        $pagerfanta = new Pagerfanta(
            new QueryAdapter($qb)
        );
        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($page);

        $collection = [];
        foreach ($pagerfanta->getCurrentPageResults() as $result) {
            $collection[] = $result;
        }

        $response = new Response(
            $this->serializer->serialize([
                'total' => $pagerfanta->getNbResults(),
                'count' => count($collection),
                'collection' => $collection,
            ], 'json'),
        200);

        return $response; */
        
    }

    /**
     * @Route("/api/corporates/{id}", name="corporate_show")
     */
    public function corporate(Request $request)
    {
        $corp = $this->serialize(
            $this->corporateRepository->find((int) $request->attributes->get('id'))
        );

        return new Response($corp, 200, ['Content-Type' => 'application/json']);
    }

    private function serialize($jsonObject)
    {
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        // Serialize your object in Json
        return  $serializer->serialize($jsonObject, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
    }
}
