<?php
/**
 * Created by
 * corentinhembise
 * 2019-05-31
 */

namespace App\Service;


use App\Entity\User;

class Notification
{
    private $message;

    /**
     * @var User[]
     */
    private $recipients;

    /**
     * @var array
     */
    private $additional;

    public function __construct($message, $recipients = [], $additional = [])
    {
        $this->message = $message;
        $this->recipients = $recipients;
        $this->additional = $additional;
    }

    /**
     * @return array
     */
    public function getAdditional(): array
    {
        return $this->additional;
    }

    /**
     * @return User[]
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}