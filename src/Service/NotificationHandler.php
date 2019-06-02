<?php
/**
 * Created by
 * corentinhembise
 * 2019-05-31
 */

namespace App\Service;


interface NotificationHandler
{
    /**
     * @param Notification $notification
     * @throws HandlerException
     */
    public function send(Notification $notification);
}