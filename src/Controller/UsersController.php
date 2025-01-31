<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Posts;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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

    #[Route('/users/update-profile', name: 'update_profile', methods: ['POST'])]
    public function updateProfile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour modifier votre profil.');
        }

        // Récupérer les valeurs du formulaire
        $bio = $request->request->get('bio');
        $username = $request->request->get('username');

        // Vérifier si le nom d'utilisateur est vide ou déjà pris
        if ($username && $username !== $user->getUsername()) {
            $existingUser = $em->getRepository(Users::class)->findOneBy(['username' => $username]);
            if ($existingUser) {
                $this->addFlash('error', 'Ce nom d\'utilisateur est déjà pris.');
                return $this->redirectToRoute('app_users');
            }
            $user->setUsername($username);
        }

        if ($bio) {
            $user->editBio($bio);
        }

        // Gestion de l'upload de la photo de profil
        $profilePicture = $request->files->get('profile_picture');
        if ($profilePicture) {
            $newFilename = uniqid() . '.' . $profilePicture->guessExtension();
            try {
                $profilePicture->move(
                    $this->getParameter('profile_pictures_directory'),
                    $newFilename
                );
                $user->setProfilePicture($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
            }
        }

        // Enregistrer les changements
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Profil mis à jour avec succès !');
        return $this->redirectToRoute('app_users');
    }
    #[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, Users $user, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $posts = $em->getRepository(Posts::class)->findBy(['user' => $user]);
            foreach ($posts as $post) {
                $em->remove($post);
            }
            $this->container->get('security.token_storage')->setToken(null);
            $em->remove($user);
            $em->flush();
        }
        $this->addFlash('deleted', 'Votre compte a été supprimé.');
        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
