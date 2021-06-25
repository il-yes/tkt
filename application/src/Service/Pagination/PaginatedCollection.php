<?php

namespace App\Service\Pagination;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class PaginatedCollection
{
	private $_links = array();

    private $items;

    private $page;

    private $pages;

    private $total;

    private $count;

    public function __construct($items, $page, $totalItems)
    {
    	$countItems = count($items);
        $this->items = $this->serialize($items);
        $this->page = $page;
        $this->total = $totalItems;
        $this->count = $countItems;
        $this->pages = ceil($countItems / $this->total);
    }

    public function items()
    {
    	return $this->items;
    }
    public function page()
    {
    	return $this->page;
    }
    public function pages()
    {
    	return $this->pages;
    }
    public function total()
    {
    	return $this->total;
    }
    public function count()
    {
    	return $this->count;
    }
    public function _links()
    {
    	return $this->_links;
    }

    public function addLink($ref, $url)
    {
        $this->_links[$ref] = $url;
    }

    public function hasNextPage()
    {
    	return $this->page < $this->pages;
    }

    public function hasPreviousPage()
    {
    	return $this->page > 1;
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
