<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'logo' => 'img/logo.png',
            'banner' => 'video/banner.mp4',
        ]);
    }

    #[Route('/404', name: 'app_error')]
    public function error(): Response
    {
        return $this->render('404.html.twig', [
            'controller_name' => 'HomeController',
            'logo' => 'img/logo.png',
        ]);
    }
}
