<?php namespace App\Controller;

use App\Repository\PostsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostsController extends AbstractController
{
    #[Route('/posts', name: 'posts_index')]
    public function index(PostsRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();

        // Rendre la page avec les publications
        return $this->render('posts/posts.html.twig', [
            'posts' => $posts,
        ]);
    }
}