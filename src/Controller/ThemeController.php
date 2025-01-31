<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ThemeController extends AbstractController
{
    #[Route('/set-theme', name: 'set_theme', methods: ['POST'])]
    public function setTheme(Request $request, SessionInterface $session)
    {
        $theme = $request->request->get('theme', 'light');
        $session->set('theme', $theme);

        return $this->redirectToRoute('/');
    }
}
