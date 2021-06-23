<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;   

class HomeController extends AbstractController
{
    /** KernelInterface $appKernel */
    private $appKernel;

    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
    }

    /**
     * @Route("/home", name="home")
     */
    public function index(): Response
    {
        $corporates = $this->corporates();
        $today = new \Datetime('2007-12-31');

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/HomeController.php',
        ]);
    }

    private function corporates(): array
    {
        $projectRoot = $this->appKernel->getProjectDir();
        return \json_decode(file_get_contents($projectRoot .'/src/DataFixtures/data.json'));
    }
}
