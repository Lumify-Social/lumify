<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Likes;
use App\Entity\Comments;
use App\Form\CommentType;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;



class PostsController extends AbstractController
{
    #[Route('/posts', name: 'posts_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $posts = $entityManager->getRepository(Posts::class)->findBy([], ['created_at' => 'DESC']);
        foreach ($posts as $post) {
            $post->likesCount = $entityManager->getRepository(Likes::class)->count(['post' => $post]);
            $post->userHasLiked = $entityManager->getRepository(Likes::class)->findOneBy([
                'post' => $post,
                'user' => $this->getUser(),
            ]) ? true : false;
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
        $post->setUser($this->getUser());


        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($post);
            $entityManager->flush();

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
            'back' => 'icons/arrow-back.png',
        ]);
    }

    #[Route('/posts/{id}/like', name: 'posts_like', methods: ['POST'])]
    public function like(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $post = $entityManager->getRepository(Posts::class)->find($id);
        if (!$post) {
            return new JsonResponse(['message' => 'Publication introuvable.'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Vérifie si l'utilisateur a déjà liké ce post
        $existingLike = $entityManager->getRepository(Likes::class)
            ->findOneBy(['post' => $post, 'user' => $this->getUser()]);

        if ($existingLike) {
            $entityManager->remove($existingLike);
            $entityManager->flush();

            $likesCount = $entityManager->getRepository(Likes::class)->count(['post' => $post]);

            return new JsonResponse(['liked' => false, 'likesCount' => $likesCount], JsonResponse::HTTP_OK);
        }

        // Crée et ajoute un like
        $like = new Likes();
        $like->setPost($post);
        $like->setUser($this->getUser());

        $post->addLike($like);
        $entityManager->persist($like);
        // $entityManager->persist($post);
        $entityManager->flush();

        // Retourne le nombre de likes et l'état du like
        $likesCount = $entityManager->getRepository(Likes::class)->count(['post' => $post]);

        return new JsonResponse(['liked' => true, 'likesCount' => $likesCount], JsonResponse::HTTP_OK);
    }

}
