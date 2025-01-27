<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Posts;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UsersController extends AbstractController
{
    #[Route('/users', name: 'app_users')]
    public function profile(EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir votre profil.');
        }

        // Récupérer les posts de l'utilisateur connecté
        $posts = $entityManager->getRepository(Posts::class)->findBy(
            ['user' => $user],  // On cherche les posts associés à cet utilisateur
            ['created_at' => 'DESC'] // Trier les posts par date de création décroissante
        );

        // Passer les posts à la vue
        return $this->render('users/index.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'logo' => 'img/logo.png',
        ]);
    }

    #[Route('/users/update-bio', name: 'update_bio', methods: ['POST'])]
    public function updateBio(Request $request, EntityManagerInterface $em): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour mettre à jour votre bio.');
        }

        // Récupérer la bio du formulaire
        $bio = $request->request->get('bio');

       

        // Enregistrer les changements dans la base de données
        $em->persist($user);
        $em->flush();

        // Rediriger vers la page de profil avec un message de succès
        $this->addFlash('success', 'Votre bio a été mise à jour !');

        return $this->redirectToRoute('app_users'); // Redirige vers la page de profil
    }
}