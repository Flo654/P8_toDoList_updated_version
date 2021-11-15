<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    /**
     * list of tasks with options (all tasks, tasks done, tasks to do)
     * 
     * @Route("/tasks/{optionList}", 
     * defaults={"optionList" = null}, 
     * requirements={"optionList"="tasksToDo|tasksDone"}, 
     * name="task_list"
     * )
     */
    public function listAction( ?string $optionList):Response
    {        
        $taskrepository = $this->getDoctrine()->getRepository('App:Task');
        switch ($optionList) {
            case 'tasksDone':                
                $taskList =  $taskrepository->findBy(['isDone' => true]);
                break;
            
            case 'tasksToDo':
                $taskList =  $taskrepository->findBy(['isDone' => false]);
                break;
                
            default:
            $taskList =  $taskrepository->findAll();
                break;
        }
       
        return $this->render('task/list.html.twig', ['tasks' => $taskList]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $task->setUser($this->getUser());
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     * @IsGranted(
     *  "TASK_EDIT", 
     *  subject="task",
     *  message="Vous n'etes pas le créateur de la tache, vous n'avez pas le droit dela modifier"
     * )
     */
    public function editAction(Task $task, Request $request)
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'La tâche a bien été modifiée.');
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task)
    {
        
        $task->toggle(!$task->isDone());
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme Effectuée.', $task->getTitle()));

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     * @IsGranted(
     *  "TASK_DELETE", 
     *  subject="task",
     *  message="Vous n'etes pas le créateur de la tache, vous n'avez pas le droit de la supprimer"
     * )
     */
    public function deleteTaskAction(Task $task)
    {
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
