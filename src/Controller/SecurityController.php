<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_users');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'logo' => 'img/logo.png',
            'last_username' => $lastUsername,
            'error' => $error]);

    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): RedirectResponse
    {
        // Personnalise la déconnexion (si nécessaire)
        // Par exemple, ajouter un message flash avant de rediriger
        $this->addFlash('success', 'Vous êtes maintenant déconnecté.');
        
        // Symfony gère la déconnexion automatiquement, donc pas besoin de plus
        return $this->redirectToRoute('app_login');
    }
}