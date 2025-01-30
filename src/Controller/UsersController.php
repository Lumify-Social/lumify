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

    // Vérifier si la bio n'est pas vide et la mettre à jour
    if ($bio) {
        $user->editBio($bio);  // Met à jour la bio de l'utilisateur
        $em->persist($user);  // Persister l'utilisateur mis à jour
        $em->flush();  // Enregistrer les changements
    }


        // Gestion de l'upload de l'image de profil
        $profilePicture = $request->files->get('profile_picture');
        
        if ($profilePicture) {
            $originalFilename = pathinfo($profilePicture->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = uniqid() . '.' . $profilePicture->guessExtension();


            try {
                // Déplacer le fichier vers le dossier 'public/uploads/profile_pictures'
                $profilePicture->move(
                    $this->getParameter('profile_pictures_directory'),
                    $newFilename
                );

                // Mettre à jour la photo de profil dans l'entité utilisateur
                $user->setProfilePicture($newFilename);
                $em->persist($user);
                $em->flush();
            } catch (FileException $e) {
                // Gérer l'erreur d'upload
                $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image.');
            }
        }

        // Rediriger vers la page de profil avec un message de succès
        $this->addFlash('success', 'Votre bio et/ou photo ont été mises à jour !');

        return $this->redirectToRoute('app_users'); // Redirige vers la page de profi
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