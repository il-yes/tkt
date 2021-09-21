<?php

namespace Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CorporateRepository;
use App\Entity\Corporate;
use Doctrine\ORM\EntityManager;


class HomeControllerTest extends WebTestCase
{
	/**
	 * @test 
	 */
	public function shouldRenderHomepage()
	{
		$client = static::createClient();
		// $client->followRedirects();
		$crawler = $client->request('GET', '/home');

		$this->assertResponseIsSuccessful();
		$this->assertSelectorTextContains('h1', 'Hello Welcome to your new controller!! ✅');
	}
}