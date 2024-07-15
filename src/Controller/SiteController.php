<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SiteController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        return $this->render('index.html.twig',[
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SiteController.php',
        ]);
    }

    #[Route('/{url}', name: 'homepage')]
    public function main(PostRepository $postRepository, CategoryRepository $categoryRepository, $url): Response
    {
        $category = $categoryRepository->findOneBy(['url' => $url]);

        if($category){
            return $this->actionCategory($category);
        }

        $post = $postRepository->findOneBy(['url' => $url]);

        if($post){
            return $this->actionCategory($category);
        }

    }

    protected function actionCategory(){
        return $this->render('index.html.twig',[
            'message' => 'Welcome to your new category!',
            'path' => 'src/Controller/SiteController.php',
        ]);
    }

    protected function actionPost(){
        return $this->render('index.html.twig',[
            'message' => 'Welcome to your new post page!',
            'path' => 'src/Controller/SiteController.php',
        ]);
    }
}
