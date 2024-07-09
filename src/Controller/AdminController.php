<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\CategoryFormType;
use App\Form\PostFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/post/list', name: 'list_post')]
    public function listPost(EntityManagerInterface $entityManager): Response
    {   
        $postRepository = $entityManager->getRepository(Post::class);
        $posts = $postRepository->findAll();

        return $this->render('admin/list-post.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/admin/post/add', name: 'add_post')]
    public function addPost(Request $request, Security $security, EntityManagerInterface $entityManager): Response
    {
        $entity = new Post();

        $form = $this->createForm(PostFormType::class, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser();
            $entity->setUser($user);

            $entityManager->persist($entity);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('admin/add-post.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/category/list', name: 'list_category')]
    public function listCategory(EntityManagerInterface $entityManager): Response
    {   
        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        return $this->render('admin/list-category.html.twig', [
            'categories' => $categories,
        ]);
    }
    
    #[Route('/admin/category/add', name: 'add_category')]
    public function addCategory(Request $request, Security $security, EntityManagerInterface $entityManager): Response
    {
        $entity = new Category();

        $form = $this->createForm(CategoryFormType::class, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entity);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('admin/add-category.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }
}
