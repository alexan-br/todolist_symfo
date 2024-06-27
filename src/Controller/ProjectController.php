<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Entity\Task;
use App\Form\ProjectType;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class ProjectController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupération de l'utilisateur connecté
        $user = $this->security->getUser();

        // Récupération des projets de l'utilisateur connecté
        $projects = $entityManager->getRepository(Project::class)->findBy(['owner' => $user]);

        // Création du formulaire pour un nouveau projet
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setOwner($user);

            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('project/index.html.twig', [
            'projectForm' => $form->createView(),
            'projects' => $projects, // Passer les projets à la vue
        ]);
    }

    // #[Route('/project/{id}/manage-users', name: 'app_project_manage_users')]
    // public function manageUsers(Project $project): Response
    // {
    //     return $this->render('project/manage_users.html.twig', [
    //         'project' => $project,
    //     ]);
    // }

    #[Route('/project/{id}/add-user', name: 'app_project_add_user')]
    public function addUser(Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($project->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not have permission to add users to this project.');
        }

        $userEmail = $request->request->get('email');
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userEmail]);

        if ($user) {
            $project->addUser($user); // Ajoutez une méthode addUser dans l'entité Project
            $entityManager->flush();
            $this->addFlash('success', 'User added to the project successfully.');
        } else {
            $this->addFlash('error', 'User not found.');
        }

        return $this->redirectToRoute('app_project_manage_users', ['id' => $project->getId()]);
    }

    #[Route('/project/{id}', name: 'app_project_view')]
    public function viewProject(Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setProject($project);
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_project_view', ['id' => $project->getId()]);
        }

        return $this->render('project/view.html.twig', [
            'project' => $project,
            'tasks' => $project->getTasks(), // Supposant que vous avez une relation bidirectionnelle
            'taskForm' => $form->createView(),
        ]);
    }
}
