<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemeController extends AbstractController
{
    #[Route('/switch-theme', name: 'switch_theme')]
    public function switchTheme(Request $request): Response
    {
        $theme = $request->getSession()->get('theme', 'light');
        $newTheme = $theme === 'light' ? 'dark' : 'light';
        $request->getSession()->set('theme', $newTheme);

        return $this->redirectToRoute('home');
    }
}

