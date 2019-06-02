<?php
/**
 * Created by
 * corentinhembise
 * 2019-05-31
 */

namespace App\Service;


class NotificationManager
{
    /**
     * @var NotificationHandler[]
     */
    private $notificationHandlers;

    public function __construct()
    {
        $this->notificationHandlers = [];
    }

    public function registerHandler(NotificationHandler $notificationHandler)
    {
        $this->notificationHandlers[] = $notificationHandler;
    }

    public function createNotification($message, $recipients, $additional = [])
    {
        return new Notification($message, $recipients, $additional);
    }

    /**
     * @param Notification $notification
     * @throws HandlerException
     */
    public function sendNotification(Notification $notification)
    {
        foreach ($this->notificationHandlers as $notificationHandler) {
            $notificationHandler->send($notification);
        }
    }
}