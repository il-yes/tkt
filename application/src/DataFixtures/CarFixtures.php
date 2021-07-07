<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Car;
use Faker\Factory; 

class CarFixtures extends Fixture
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
            $corp = new Car();

            $corp->setBrand($this->faker->randomElement($array = [
                    'lamborghini', 'maseratti', 'alfa-romeo', 'mercedes', 'audi', 'porsche'
                ]))
                ->setModel($this->faker->name())
                ->setCategory($this->faker->name())
                ->setOwner($this->faker->firstName())
                ->setYear($this->faker->year($max = 'now') );

            $manager->persist($corp);
        }

        $manager->flush(); 
    }
}
