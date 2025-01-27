<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BackgroundController extends AbstractController
{
    public function changerFond(Request $request, SessionInterface $session): Response
    {
        $background = $request->request->get('background');

        $session->set('background', $background);

        return $this->redirectToRoute('/');
    }
}
