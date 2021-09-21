<?php

namespace Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;


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