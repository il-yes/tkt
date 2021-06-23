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


class CorporateController extends AbstractController
{
    private $corporateRepository;
    private  $serializer;

    public function __construct(
        CorporateRepository $repository, SerializerInterface $serializeer
    )
    {
        $this->corporateRepository = $repository;
        $this->serializer = $serializeer;
    }

    /**
     * @Route("/api/corporates", name="corporate_list")
     */
    public function index(Request $request): Response
    {
        // numero de la page
        $page = (int)$request->query->get('page', 1);
        $limit = (int)$request->query->get('limit', $this->corporateRepository::LIMIT);
        $order = $request->query->get('order', 'asc');

        if ($request->query->get('name') !== null) {
            $this->corporateRepository->byName($request->query->get('name'));
        }
        if ($request->query->get('order') !== null) {
            $this->corporateRepository->byOrder($order);
        }
        $collection = $this->serialize(
            $this->corporateRepository->findPaginatedCorporates($page, $limit)
        );

        $skills = "";
        // Total collection
        $total = $this->corporateRepository->totalCorporates();

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
