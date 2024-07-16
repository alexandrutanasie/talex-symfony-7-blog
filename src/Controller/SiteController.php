<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SiteController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(PostRepository $postRepository): Response
    {
        $latestPosts = $postRepository->findLatestArticles();

        return $this->render('index.html.twig',[
            'latestPosts' => $latestPosts
        ]);
    }

    #[Route('/{url}', name: 'main_url')]
    public function main(PostRepository $postRepository, CategoryRepository $categoryRepository, $url): Response
    {
        $category = $categoryRepository->findOneBy(['url' => $url]);

        if($category){
            return $this->actionCategory($category);
        }

        $post = $postRepository->findOneBy(['url' => $url]);

        if($post){
            return $this->actionPost($post);
        }

        return $this->render("error404.html.twig");

    }

    protected function actionCategory($category){
            
        return $this->render('site/category_view.html.twig',[
            'category' => $category
        ]);
    }

    protected function actionPost($post){
        return $this->render('site/post_view.html.twig',[
            'post' => $post
        ]);
    }
}
