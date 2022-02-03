<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/user", name="admin_user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/toggle-status/{id}", name="toggle_status")
     */
    public function toggleStatus(EntityManagerInterface $em, UserRepository $userRepository, int $id): Response
    {
        // $user = $em->getRepository(User::class)->findOneBy(["id" => $id]);
        try {
            // Modify status
            $user = $userRepository->findOneBy(["id" => $id]);
            $user->setStatus(!$user->getStatus());
            $em->persist($user);
            $em->flush();
            $this->addFlash("success", "User updated.");
        } catch (\Throwable $th) {
            //throw $th;
            $this->addFlash("danger", "User not updated.");
        }

        return $this->redirectToRoute("admin_user_index");
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        // Deleting user
        // User to delete is defined in the "automatic variables of the function"
        // $user = $em->getRepository(User::class)->findOneBy(["id" => $id]);
        try {
            // Delete user
            $em->remove($user);
            $em->flush();
            $this->addFlash("success", "User deleted.");
        } catch (\Throwable $th) {
            //throw $th;
            $this->addFlash("danger", "Error deleting user: $th->getMessage()");
        }

        return $this->redirectToRoute("admin_user_index");
    }
}
