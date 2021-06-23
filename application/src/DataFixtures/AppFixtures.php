<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Corporate;
use App\Entity\Result;

class AppFixtures extends Fixture
{
    private $appDirectory;

    public function __construct($appDirectory)
    {
        $this->appDirectory = $appDirectory;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->corporates();

        foreach($data as $corporate) {
            $corp = new Corporate();

            $corp->setName($corporate->name)
                ->setSiren($corporate->siren)
                ->setSector($corporate->sector);

            foreach($corporate->results as $result) {
                $r = new Result();
                $r->setCa($result->ca)
                    ->setMargin($result->margin)
                    ->setEbitda($result->ebitda)
                    ->setLoss($result->loss);
                $date = new \Datetime($result->year .'-12-31');
                $r->setYear($date);
                
                $manager->persist($r);
                $corp->addResult($r);
            }

            $manager->persist($corp);
        }

        $manager->flush(); 
    }

    private function corporates(): array
    {
        return \json_decode(file_get_contents($this->appDirectory .'DataFixtures/data.json'));
    }
}
