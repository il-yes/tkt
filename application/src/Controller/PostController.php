<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Pagination\PaginatedCollection;
use App\Service\Pagination\PaginationFactory;
use App\Repository\FindPostsQuery;
use App\Repository\PostRepository;
use Symfony\Component\Serializer\SerializerInterface;
use App\Controller\BaseController;

class PostController extends BaseController
{
	private $postRepository;
    private $paginationFactory;
    private $findPostsQuery;

	public function __construct(
        PostRepository $repository, 
        SerializerInterface $serializer,
        PaginationFactory $paginationFactory,
        FindPostsQuery $findPostsQuery
    )
    {
        $this->postRepository = $repository;
        $this->serializer = $serializer;
        $this->paginationFactory = $paginationFactory;
        $this->findPostsQuery = $findPostsQuery;
    }

    /**
     * @Route("/api/posts", name="posts_list")
     */
    public function index(Request $request): Response
    {
    	$range  = \json_decode($request->query->get('range'));

        $this
        	->findPostsQuery
            ->init(
             	$range, 
             	\json_decode($request->query->get('sort')), 
             	\json_decode($request->query->get('page')),
             	\json_decode($request->query->get('perPage')), 
             	(array) \json_decode($request->query->get('filter'))
            );

		$qb = $this->findPostsQuery->execute();

        $paginatedCollection = $this
                                ->paginationFactory
                                ->createCollection(
                                	$qb, 
                                	$request, 
                                	'posts_list', 
                                	$this->findPostsQuery->getPage(),
                                	$this->findPostsQuery->getPerPage()
                                );

        return $this->createApiResponseForReactAdmin(
        	$this->findPostsQuery->getRange()[0], $paginatedCollection, 'posts'
        );  
    }

}
