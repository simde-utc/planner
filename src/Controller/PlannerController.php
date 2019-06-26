<?php
/**
 * Created by
 * corentinhembise
 * 2019-06-12
 */

namespace App\Controller;


use App\Entity\Event;
use App\Repository\UserRepository;
use App\Repository\UserTaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class PlannerController extends AbstractController
{
    public function userList(Event $event, UserRepository $userRepository, SerializerInterface $serializer)
    {
        $users = $userRepository->getAvailableUsersForEvent($event, true);

        return $this->json($users);
    }

    public function userTaskList(Event $event, UserTaskRepository $userTaskRepository)
    {
        $userTask = $userTaskRepository->getUserTaskForEvent($event, true, true);

        return $this->json($userTask);
    }
}