<?php
namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostType;
use App\Entity\Comments;
use App\Form\CommentType;
use App\Repository\CommentsRepository;
use App\Repository\PostsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostsController extends AbstractController
{
    #[Route('/posts', name: 'posts_index')]
    public function index(PostsRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();

        return $this->render('posts/posts.html.twig', [
            'posts' => $posts,
            'logo' => 'img/logo.png',
        ]);
    }

    #[Route('/posts/create', name: 'posts_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Posts();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('posts_index');
        }
        return $this->render('posts/create.html.twig', [
            'form' => $form->createView(),
            'logo' => 'img/logo.png',
        ]);
    }
    #[Route('/posts/{id}', name: 'posts_show')]
public function show(int $id, Request $request, PostsRepository $postsRepository, EntityManagerInterface $entityManager): Response
{
    $post = $postsRepository->find($id);

    if (!$post) {
        throw $this->createNotFoundException('Publication introuvable.');
    }

    $comment = new Comments();
    $commentForm = $this->createForm(CommentType::class, $comment);
    $commentForm->handleRequest($request);

    if ($commentForm->isSubmitted() && $commentForm->isValid()) {
        $comment->setPost($post);
        $comment->setUser($this->getUser());
        $comment->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('posts_show', ['id' => $post->getId()]);
    }

    return $this->render('posts/show.html.twig', [
        'post' => $post,
        'comment_form' => $commentForm->createView(),
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
        ]);
    }


    
}

