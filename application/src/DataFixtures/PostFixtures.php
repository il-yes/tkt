<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Post;
use Faker\Factory; 

class PostFixtures extends Fixture
{
    /** @var  Generator $faker */    
    protected $faker;

    public function __construct($appDirectory)
    {
        $this->faker = Factory::create();
        $this->appDirectory = $appDirectory;
    }

    public function load(ObjectManager $manager)
    {
        for($i = 0; $i <= 1000; $i++) {
            $corp = new Post();

            $corp->setTitle($this->faker->text($maxNbChars = 100))
                ->setBody($this->faker->text($maxNbChars = 100))
                ->setpublishedAt(
                    $this->faker->dateTimeThisDecade($max = 'now', $timezone = 'Europe/Paris') 
                );

            $manager->persist($corp);
        }

        $manager->flush(); 
    }
}
