<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    /**
     * @Route("/post", name="post")
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        
        
        $title = $request->request->get("title");
        $content = $request->request->get("content");
        
        if (isset($title) && $title!="") {
            $post->setTitle($title);
            $post->setContent($content);

            $em->persist($post);
            $em->flush();
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

        $posts = $em->getRepository(Post::class)->findAll();
        // dd($posts);
        return $this->redirectToRoute("post");
    }
}
