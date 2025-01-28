<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TrendsController extends AbstractController
{
    #[Route('/trends', name: 'app_trends')]
    public function index(): Response
    {
        return $this->render('trends/index.html.twig', [
            'controller_name' => 'TrendsController',
            'logo' => 'img/logo.png',
        ]);
    }
}
