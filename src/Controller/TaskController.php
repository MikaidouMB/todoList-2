<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    private EntityManagerInterface $em;
    private TaskRepository $taskRepository;

    public function __construct(EntityManagerInterface $em, TaskRepository $taskRepository) {
        $this->em = $em;
        $this->taskRepository = $taskRepository;
    }

    /**
     * @Route("/tasks", name="task_list")
     */
    public function listAction(): Response
    {
        $this->em->getRepository(Task::class);
        $tasks = $this->taskRepository->findAll();
        return $this->render('task/list.html.twig', [
            'tasks' => $tasks
        ]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request): RedirectResponse|Response
    {
        if ($this->getUser()) {
            $task = new Task();
            $form = $this->createForm(TaskType::class, $task);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $task->setUser($this->getUser());
                $this->em->persist($task);
                $this->em->flush();

                $this->addFlash('success', 'La tâche a été bien été ajoutée.');

                return $this->redirectToRoute('task_list');
            }
        }else {
            return $this->redirectToRoute('homepage');
        }
       return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editAction(Task $task, Request $request):Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {

            $form = $this->createForm(TaskType::class, $task);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->em->flush();

                $this->addFlash('success', 'La tâche a bien été modifiée.');

                return $this->redirectToRoute('task_list');
            }
            return $this->render('task/edit.html.twig', [
                'form' => $form->createView(),
                'task' => $task,
            ]);
        }

        if ($task->getUser() !== null && $this->getUser()->getId() == $task->getUser()->getId()) {

            $form = $this->createForm(TaskType::class, $task);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->em->flush();

                $this->addFlash('success', 'La tâche a bien été modifiée par l\'auteur.');

                return $this->redirectToRoute('task_list');
            }
            return $this->render('task/edit.html.twig', [
                'form' => $form->createView(),
                'task' => $task,
            ]);
        }

        $this->addFlash('danger', 'Vous ne pouvez pas modifier cette tâche.');

        return $this->redirectToRoute('task_list');
    }


    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task): RedirectResponse
    {
        if ($this->isGranted('ROLE_ADMIN')){
            $task->toogle(!$task->isDone());

            $this->em->flush();

            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
            return $this->redirectToRoute('task_list');

        }

        if ($task->getUser() !== null && $this->getUser() === $task->getUser()) {
            $task->toogle(!$task->isDone());

            $this->em->flush();

            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite par l\'auteur.', $task->getTitle()));
            return $this->redirectToRoute('task_list');

        }

        $this->addFlash('danger', 'Vous ne pouvez pas marquer cette tâche.');
        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Task $task): RedirectResponse
    {
        if ($this->getUser() !== null) {
            if ($this->isGranted('ROLE_ADMIN')){
                $this->em->remove($task);
                $this->em->flush();

                $this->addFlash('success', 'La tâche a bien été supprimée par vous administrateur.');

                return $this->redirectToRoute('task_list');
            }

             if ($this->getUser()->getId() == $task->getUser()->getId()){
                    $this->em->remove($task);
                    $this->em->flush();

                    $this->addFlash('success', 'La tâche a bien été supprimée.');

                    return $this->redirectToRoute('task_list');
                }
            }

        $this->addFlash('danger', 'Vous ne pouvez pas supprimer cette tâche.');
        return $this->redirectToRoute('task_list');

    }
}
