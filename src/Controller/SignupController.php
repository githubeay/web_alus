<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class SignupController extends AbstractController
{
    /**
     * @Route("/signup", name="signup")
     */
    public function index(
        Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher,
        UserAuthenticatorInterface $authenticator, FormLoginAuthenticator $formAuthenticator
        ): Response
    {
        if ($this->getUser()) {
            // Return to homepage
            return $this->redirectToRoute("post");
        }

        $user = new User();
        $form = $this->createForm(SignupType::class, $user);

        // Add user
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Password encryption
            $plainPasswd = $user->getPassword();
            $hashedPasswd = $hasher->hashPassword($user, $plainPasswd);
            $user->setPassword($hashedPasswd);
            // Persist
            $em->persist($user);
            $em->flush();
            $this->addFlash("success", "User has been added successfully!");

            // Authentication and redirection
            $authenticator->authenticateUser(
                $user,
                $formAuthenticator,
                $request);
            return $this->redirectToRoute("post");
        }

        return $this->render('signup/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
