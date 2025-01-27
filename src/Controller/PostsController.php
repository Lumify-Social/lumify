<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostType;
use App\Entity\Comments;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Likes;

class PostsController extends AbstractController
{
    #[Route('/posts', name: 'posts_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $posts = $entityManager->getRepository(Posts::class)->findBy([], ['created_at' => 'DESC']);
        foreach ($posts as $post) {
            $post->likesCount = $entityManager->getRepository(Likes::class)->count(['post' => $post]);
        }
    
        return $this->render('posts/posts.html.twig', [
            'posts' => $posts,
            'logo' => 'img/logo.png',
        ]);
    }

    #[Route('/posts/create', name: 'posts_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Posts();
        $post->setUser($this->getUser()); // Associe l'utilisateur connecté

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCreatedAt(new \DateTimeImmutable()); // Initialise la date de création
            $entityManager->persist($post);
            $entityManager->flush();

            // Gérer les requêtes AJAX
            if ($request->isXmlHttpRequest()) {
                return $this->json(['message' => 'Publication créée avec succès !'], Response::HTTP_OK);
            }

            return $this->redirectToRoute('posts_index');
        }

        return $this->render('posts/create.html.twig', [
            'form' => $form->createView(),
            'logo' => 'img/logo.png',
        ]);
    }

    #[Route('/posts/{id}', name: 'posts_show', methods: ['GET', 'POST'])]
    public function show(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = $entityManager->getRepository(Posts::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Publication introuvable.');
        }

        $comment = new Comments();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPost($post);
            $comment->setUser($this->getUser());
            $comment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('posts_show', ['id' => $post->getId()]);
        }

        return $this->render('posts/show.html.twig', [
            'post' => $post,
            'comment_form' => $form->createView(),
            'logo' => 'img/logo.png',
        ]);
    }

    public function sidebar(): Response
    {
        return $this->render('posts/sidebar.html.twig', [
            'home' => 'icons/home.png',
            'posts' => 'icons/posts.png',
            'search' => 'icons/search.png',
            'notifications' => 'icons/notifications.png',
            'profile' => 'icons/profile.png',
            'settings' => 'icons/settings.png',
            'logout' => 'icons/logout.png',
            'plus' => 'icons/plus.png',
            'like' => 'icons/like.png',
            'comment' => 'icons/comment.png',
            'repost' => 'icons/repost.png',
        ]);
    }
    #[Route('/posts/{id}/like', name: 'posts_like', methods: ['POST'])]
    public function like(int $id, EntityManagerInterface $entityManager): Response
    {
        $post = $entityManager->getRepository(Posts::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Publication introuvable.');
        }

        // Vérifier si l'utilisateur a déjà liké ce post
        $existingLike = $entityManager->getRepository(Likes::class)
            ->findOneBy(['post' => $post, 'user' => $this->getUser()]);

        if ($existingLike) {
            return $this->json(['message' => 'Vous avez déjà liké cette publication.'], Response::HTTP_BAD_REQUEST);
        }

        // Créer un nouveau like
        $like = new Likes();
        $like->setPost($post);
        $like->setUser($this->getUser());

        $entityManager->persist($like);
        $entityManager->flush();

        return $this->json(['message' => 'Publication likée avec succès.'], Response::HTTP_OK);
    }
}

