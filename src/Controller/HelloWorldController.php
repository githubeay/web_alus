<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelloWorldController extends AbstractController
{
    /**
     * @Route("/hello/world", name="hello_world")
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $post = new Post();
        // $post->setTitle('First Post');
        // $post->setContent('This is our first post');

        // $em->persist($post);
        // $em->flush();

        $name = $request->query->get("name");
        if (!isset($name)){
            $name = "Alous";
        }
        // dd($name);
        return $this->render('hello_world/index.html.twig', [
            'controller_name' => 'HelloWorldController',
            'name' => $name,
        ]);
    }
}
