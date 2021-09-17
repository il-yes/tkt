<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface; 
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
  

class BaseController extends AbstractController
{
    protected  $serializer;


 	protected function createApiResponseForReactAdmin($start, $paginator, $namespace)
    {
        // some comments....
        
        return new Response(
            $this->serializer->serialize($paginator->items(), 'json'),
            200, 
            [
                'Content-Type' => 'application/json',
                'Access-Control-Expose-Headers' => 'X-Total-Count',
                'Access-Control-Expose-Headers' => 'content-range',
                'X-Total-Count' => $paginator->count(),
                'content-range' => $namespace ." ". $start ."-". $paginator->total() ."/". $paginator->count()
            ]
        );
    }

    protected function serialize($jsonObject)
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