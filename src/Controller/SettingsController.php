<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SettingsController extends AbstractController
{
    #[Route('/settings', name: 'app_settings')]
    public function index(): Response
    {
        return $this->render('settings/index.html.twig', [
            'controller_name' => 'SettingsController',
            'logo' => 'img/logo.png',
            'blue-steel' => 'themes/blue-steel.png',
            'gotham' => 'themes/gotham.png',
            'gunmetal' => 'themes/gunmetal.png',
            'midnight' => 'themes/midnight.png',
            'purple-burst' => 'themes/purple-burst.png',
            'space' => 'themes/space.png',
        ]);
    }
}
