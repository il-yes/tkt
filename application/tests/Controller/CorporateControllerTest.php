<?php

namespace Tests\Controller;


use Tests\Framework\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CorporateRepository;
use App\Entity\Corporate;
use Doctrine\ORM\EntityManager;


class CorporateControllerTest extends ApiTestCase
{
/**
    * @var CorporateRepository
    */
    private $corporateRepository;

    
    public function setUp(): void
    {
        parent::setUp();
        $this->corporateRepository = $this->getEntityManager()->getRepository(
            Corporate::class
        );
    }

    /**
    * @test
    */
    public function shouldrenderAllCorporates()
    {
        $this->visit('/api/corporates')
                ->assertResponseOk();
        $response = $this->client->get('/api/corporates');
        $corps = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('pagination', $corps);
        $this->assertArrayHasKey('collection', $corps);
        $this->assertEquals(10, $corps['pagination']['limit']);
    }

    /**
    * @test
    */
    public function shouldRenderAllCorporatesPaginated()
    {
        $this->visit('/api/corporates?page=7')
                ->assertResponseOk();
        $response = $this->client->get('/api/corporates?page=7');
        $corps = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('pagination', $corps);
        $this->assertArrayHasKey('collection', $corps);
        $this->assertEquals(10, $corps['pagination']['limit']);
        $this->assertEquals(61, json_decode($corps['collection'])[0]->id);

        $nextUrl = $corps['pagination']['_links']['next'];
        $response = $this->client->get($nextUrl);
        $corps = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('pagination', $corps);
        $this->assertArrayHasKey('collection', $corps);
        $this->assertEquals(10, $corps['pagination']['limit']);
        $this->assertEquals(71, json_decode($corps['collection'])[0]->id);

        $prevUrl = $corps['pagination']['_links']['prev'];
        $response = $this->client->get($prevUrl);
        $corps = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('pagination', $corps);
        $this->assertArrayHasKey('collection', $corps);
        $this->assertEquals(10, $corps['pagination']['limit']);
        $this->assertEquals(61, json_decode($corps['collection'])[0]->id);
    }

    /**
    * @test
    */
    public function shouldRenderOneCorporate()
    {
        $this->visit('/api/corporates/10')
                ->assertResponseOk();
        $response = $this->client->get('/api/corporates/10');
        $corp = json_decode($response->getBody(), true);

        $this->assertEquals('10', $corp['id']);
        $this->assertArrayHasKey('results', $corp);

        //var_dump(json_decode($response->getBody(), true));
    }

    /**
    * @test
    */
    public function shouldRenderCorporatesByFiltering()
    {
        $apiResponse = $this
                        ->client
                        ->get('/api/corporates?filter={"sector":"energy"}');

        $response = \json_decode($apiResponse->getBody(), true);
        $collection = \json_decode($response['collection']);

        $this->assertNotEmpty($response);
        $this->assertNotEmpty($collection);
        $this->assertEquals('energy', strtolower($collection[0]->sector));

        $nextUrl = $response['pagination']['_links']['next'];
        $apiResponse = $this->client->get($nextUrl);
        $response = json_decode($apiResponse->getBody(), true);

        $this->assertArrayHasKey('pagination', $response);
        $this->assertArrayHasKey('collection', $response);
        $this->assertEquals(10, $response['pagination']['limit']);
        
        $collection = \json_decode($response['collection']);
        $this->assertEquals('energy', strtolower($collection[0]->sector));
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager(): EntityManager
    {
        return $this->getService('doctrine.orm.entity_manager');
    }

}