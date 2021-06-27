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
        $page   = (int)$request->query->get('page', 1);
        $limit  = (int)$request->query->get('limit', CorporateRepository::LIMIT);
        $order  = $request->query->get('order', 'asc');
        $filter = \json_decode($request->query->get('filter'));

        $this->initializeParams($request, $order, $filter, $this->corporateRepository);

        $qb = $this
                ->corporateRepository
                ->findPaginatedCorporates(
                    $page, 
                    $limit
                );

        $paginatedCollection = $this
                                ->paginationFactory
                                ->createCollection($qb, $request, 'corporate_list');

        return $this->createApiResponse($paginatedCollection);
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

    private function initializeParams(Request $request, $order, $filter, $repository)
    {
        if($request->query->get('name') !== null) {
            $repository->byName($request->query->get('name'));
        }
        if($request->query->get('order') !== null) {
            $repository->byOrder($order);
        }
        if($request->query->get('filter') !== null) {
            $repository->byFilter($filter);
        }
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
            ['Content-Type' => 'application/json']
        );
    }
}
