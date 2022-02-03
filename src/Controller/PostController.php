<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\KernelInterface;

class PostController extends AbstractController
{
    /**
     * @Route("/post", name="post")
     */
    public function index(Request $request, EntityManagerInterface $em, KernelInterface $kernel): Response
    {
        $post = new Post();
        // dd($kernel->getProjectDir());
        $form = $this->createForm(PostType::class, $post);
        
        // Méthode classique
        // 
        // $title = $request->request->get("title");
        // $content = $request->request->get("content");
        // 
        // if (isset($title) && $title!="") {
        //     $post->setTitle($title);
        //     $post->setContent($content);
        //     $em->persist($post);
        //     $em->flush();
        // }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imgPath = $kernel->getProjectDir() . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "images";
                // dd($imageFile->getClientOriginalName());
                $imgName = uniqid('perso') . $imageFile->guessExtension();
                $imageFile->move($imgPath, $imgName);
                $post->setImage($imgName);
            }
            $em->persist($post);
            $em->flush();
            $this->addFlash("success", "Post has been added successfully!");
        }

        $posts = $em->getRepository(Post::class)->findAll();
        // dd($posts);
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
            'posts' => $posts,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post/delete/{id}", name="post_delete")
     */
    public function delete(Post $post, EntityManagerInterface $em): Response
    {
        // Symfony fait un maching automatique de id avec celui de la classe Post (Paramconverter)
        $em->remove($post);
        $em->flush();

        // $posts = $em->getRepository(Post::class)->findAll();
        // dd($posts);
        return $this->redirectToRoute("post");
    }

    /**
     * @Route("/post/edit/{id}", name="post_edit")
     */
    public function edit(Request $request, String $id, EntityManagerInterface $em): Response
    {
        // Get post with id
        $post = $em->getRepository(Post::class)->findOneBy(['id'=>$id]);

        // Class form hydrated with instance $post
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($post);
            $em->flush();
            $this->addFlash("success", "Post has been updated successfully!");
            return $this->redirectToRoute('post');
        }
        
        // dd($posts);
        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
}
