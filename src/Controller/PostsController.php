<?php

namespace App\Controller;
use App\Form\PostType;
use App\Entity\Posts;
use App\Entity\Likes;
use App\Entity\Comments;
use App\Form\PostType;
use App\Form\CommentType;
use App\Form\PostType;
use App\Entity\Repost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PostsController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

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

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    $post->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image.');
                }
            }

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('posts_index');
        }

        return $this->render('posts/create.html.twig', [
            'form' => $form->createView(),
            'logo' => 'img/logo.png',
        ]);
    }
    #[Route('/posts/{id}/delete', name: 'posts_delete', methods: ['POST'])]
    public function delete(Posts $post, EntityManagerInterface $em): RedirectResponse
    {
        // vérifie si l'utilisateur connecté est celui qui a crée le post
        if ($this->getUser() === $post->getUser()) {
            // Supprime le post de la bdd
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('posts_index'); 
    }

    #[Route('/posts/{id}', name: 'posts_show', methods: ['GET', 'POST'])]
    public function show(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = $entityManager->getRepository(Posts::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Publication introuvable.');
        }

        $post->likesCount = $entityManager->getRepository(Likes::class)->count(['post' => $post]);
        $post->userHasLiked = $entityManager->getRepository(Likes::class)->findOneBy([
            'post' => $post,
            'user' => $this->getUser(),
        ]) ? true : false;

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
        $post = $entityManager->getRepository(Posts::class)->find($id);
        $post->likesCount = $entityManager->getRepository(Likes::class)->count(['post' => $post]);
        $post->userHasLiked = $entityManager->getRepository(Likes::class)->findOneBy(['post' => $post, 'user' => $this->getUser(),]) ? true : false;
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
        $entityManager->flush();

        // Retourne le nombre de likes et l'état du like
        $likesCount = $entityManager->getRepository(Likes::class)->count(['post' => $post]);

        return new JsonResponse(['liked' => true, 'likesCount' => $likesCount], JsonResponse::HTTP_OK);
    }

    #[Route('/repost/{id}', name: 'post_repost')]
    public function repost(Posts $post, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();
    
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être connecté pour reposter.");
        }
    
        $repost = new Posts();
        $repost->setUser($user);
        $originalPost = $entityManager->getRepository(Repost::class)->find($post->getId());

        if ($originalPost) {
            $repost->setContent($originalPost->getContent());
    
            $repost->setOriginalPost($originalPost);
        
            $entityManager->persist($repost);
            $entityManager->flush();
        } else {
            throw $this->createNotFoundException('Le post original est introuvable.');
        }
    
        return $this->redirectToRoute('posts');
    }
}
