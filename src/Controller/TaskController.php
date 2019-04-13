<?php
/**
 * Created by
 * corentinhembise
 * 2019-03-26
 */

namespace App\Controller;


use App\Entity\Event;
use App\Entity\Skill;
use App\Entity\Task;
use App\Form\TaskType;
use Colors\RandomColor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
    public function tasks(Event $event)
    {
        $tasks = $event->getTasks();
        return $this->render('event/tasks/show.html.twig', [
            'event' => $event,
            'task'  =>  $tasks ? $tasks->first() : null,
        ]);
    }

    public function new(Event $event, Request $request)
    {
        $task = new Task();
        $task->setEvent($event);

        $form = $this->createForm(TaskType::class, $task, [
            'event' => $event,
        ]);
        $form->add('submit', SubmitType::class, [
            'label' => 'Créer',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('event_tasks_show', [
                'id' => $event->getId(),
                'task_id' => $task->getId(),
            ]);
        }

        return $this->render('event/tasks/new.html.twig', [
            'form'  => $form->createView(),
            'event' => $event,
        ]);
    }

    /**
     * @param Event $event
     * @param Task $task
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("task", class="App\Entity\Task",  options={"mapping": {"task_id": "id"}})
     */
    public function edit(Event $event, Task $task, Request $request)
    {
        $form = $this->createForm(TaskType::class, $task, [
            'precision' => $event->getTimePrecision(),
            'event' => $event,
        ]);
        $form->add('submit', SubmitType::class, [
            'label' => 'Enregistrer',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('event_tasks_show', [
                'id' => $event->getId(),
                'task_id' => $task->getId(),
            ]);
        }

        return $this->render('event/tasks/edit.html.twig', [
            'form'  => $form->createView(),
            'event' => $event,
            'task'  => $task,
        ]);
    }

    /**
     * @param Event $event
     * @param Task $task
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("task", class="App\Entity\Task",  options={"mapping": {"task_id": "id"}})
     */
    public function show(Event $event, Task $task)
    {
        return $this->render('event/tasks/show.html.twig', [
            'event' => $event,
            'task'  => $task,
        ]);
    }

    /**
     * @param Event $event
     * @param Task $task
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @ParamConverter("task", class="App\Entity\Task",  options={"mapping": {"task_id": "id"}})
     */
    public function delete(Event $event, Task $task)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($task);
        $em->flush();

        return $this->redirectToRoute('event_tasks', [
            'id' => $event->getId(),
        ]);
    }
}