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
use App\Repository\FindCorporatesQuery;
use App\Repository\FindCorpsQuery;

class CorporateController extends BaseController
{
    private $corporateRepository;
    private  $paginationFactory;
    private  $findCorporatesQuery;
    private  $findCorpsQuery;

    public function __construct(
        CorporateRepository $repository, 
        SerializerInterface $serializer,
        PaginationFactory $paginationFactory,
        FindCorporatesQuery $findCorporatesQuery,
        FindCorpsQuery $findCorpsQuery
    )
    {
        $this->corporateRepository = $repository;
        $this->serializer = $serializer;
        $this->paginationFactory = $paginationFactory;
        $this->findCorpsQuery = $findCorpsQuery;
    }

    /**
     * @Route("/corporates", name="corporate_list")
     */
    public function corporates(Request $request): Response
    {
        $page   = (int)$request->query->get('page', 1);
        $limit  = (int)$request->query->get(
            'limit', $this->findCorporatesQuery::LIMIT
        );
        $order  = $request->query->get('order', 'desc');
        $filter = \json_decode($request->query->get('filter'));

        $this->findCorporatesQuery
             ->initializeParams($request, $order, $filter);

        $qb = $this
                ->findCorporatesQuery
                ->execute($page, $limit);

        $paginatedCollection = $this
                                ->paginationFactory
                                ->createCollection($qb, $request, 'corporate_list', $page, $limit);

        return $this->createApiResponseForReactAdmin(
            0, $paginatedCollection, 'corporates'
        );
    }


    /**
     * @Route("/api/corporates", name="corporate_list")
     */
    public function index(Request $request): Response
    {
        $range  = \json_decode($request->query->get('range'));

        $this
            ->findCorpsQuery
            ->init(
                $range, 
                \json_decode($request->query->get('sort')), 
                \json_decode($request->query->get('page')),
                \json_decode($request->query->get('perPage')), 
                (array) \json_decode($request->query->get('filter'))
            );

        $qb = $this->findCorpsQuery->execute();

        $paginatedCollection = $this
                                ->paginationFactory
                                ->createCollection(
                                    $qb, 
                                    $request, 
                                    'corporate_list', 
                                    $this->findCorpsQuery->getPage(),
                                    $this->findCorpsQuery->getPerPage()
                                );
    //dd($this->findCorpsQuery);

        //return $this->createApiResponse($paginatedCollection);
        return $this->createApiResponseForReactAdmin(
            $this->findCorpsQuery->getRange()[0], $paginatedCollection, 'corporates'
        );
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

    private function createApiResponse($paginator)
    {
        return new Response(
            $this->serializer->serialize([
                'pagination' => [
                    'page' => $paginator->page(),
                    'pages' => $paginator->pages(),
                    'limit' => $paginator->total(),
                    'total' => $paginator->count(),
                    '_links' => $paginator->_links()
                ],
                'collection' => $paginator->items()                
            ], 'json'),
            200, 
            [
                'Content-Type' => 'application/json',
                'Access-Control-Expose-Headers' => 'X-Total-Count',
                'X-Total-Count' => $paginator->count()  
            ]
        );
    }

}
