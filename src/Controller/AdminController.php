<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\CategoryFormType;
use App\Form\ChangePasswordFormType;
use App\Form\PostFormType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/admin")]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/post/list', name: 'list_post')]
    public function listPost(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();

        return $this->render('admin/post/list.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post/add', name: 'add_post')]
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

            return $this->redirectToRoute('list_post');
        }

        return $this->render('admin/post/form.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/post/{id}/edit", name: "post_edit")]
    public function editPost(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostFormType::class, $post);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Post edited successfully.');
            return $this->redirectToRoute('list_post');
        }
        
        return $this->render('admin/post/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/post/{id}/delete", name: "post_delete")]
    public function deletePost(Request $request, $id, PostRepository $postRepository,  EntityManagerInterface $entityManager): Response
    {
        $post = $postRepository->find($id);
        
        if (!$post) {
            throw $this->createNotFoundException('No post found for id ' . $id);
        }

        // Delete the post
        $entityManager->remove($post);
        $entityManager->flush();

        // Optionally add a flash message for user feedback
        $this->addFlash('success', 'Post deleted successfully.');
        return $this->redirectToRoute('list_post');
    }

    #[Route('/category/list', name: 'list_category')]
    public function listCategory(EntityManagerInterface $entityManager): Response
    {   
        $categoryRepository = $entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        return $this->render('admin/category/list.html.twig', [
            'categories' => $categories,
        ]);
    }
    
    #[Route('/category/add', name: 'add_category')]
    public function addCategory(Request $request, Security $security, EntityManagerInterface $entityManager): Response
    {
        $entity = new Category();

        $form = $this->createForm(CategoryFormType::class, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entity);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('list_category');
        }

        return $this->render('admin/category/form.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/category/{id}/edit", name: "category_edit")]
    public function editCategory(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryFormType::class, $category);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Category edited successfully.');
            return $this->redirectToRoute('list_category');
        }
        
        return $this->render('admin/category/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/category/{id}/delete", name: "category_delete")]
    public function deleteCategory(Request $request, $id, CategoryRepository $categoryRepository,  EntityManagerInterface $entityManager): Response
    {
        $category = $categoryRepository->find($id);
        
        if (!$category) {
            throw $this->createNotFoundException('No category found for id ' . $id);
        }

        // Delete the post
        $entityManager->remove($category);
        $entityManager->flush();

        // Optionally add a flash message for user feedback
        $this->addFlash('success', 'Category deleted successfully.');
        return $this->redirectToRoute('list_categories');
    }

    #[Route('/user/change-password', name: 'change_password')]
    public function changePassword(Request $request, Security $security, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneBy(["id" => $security->getUser()->getId()]);

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $newPassword = $form->get('newPassword')->getData();
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $newPassword)
            );
            $entityManager->flush();

            $this->addFlash('success', 'Password changed successfully.');

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('admin/user/change-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
