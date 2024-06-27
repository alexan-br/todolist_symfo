<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class AdminController extends AbstractController
{
    #[Route('/admin/users', name: 'admin_user_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function userList(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/user_list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/edit/{id}', name: 'admin_user_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function editUser(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user_edit.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/delete/{id}', name: 'admin_user_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_user_list');
    }
}
