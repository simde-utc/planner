<?php
/**
 * Created by
 * corentinhembise
 * 2019-05-31
 */

namespace App\Service;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class PortalNotificationHandler implements NotificationHandler
{
    /**
     * @var Client
     */
    private $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param Notification $notification
     * @throws HandlerException
     */
    public function send(Notification $notification)
    {
        foreach ($notification->getRecipients() as $recipient) {
            $body['content'] = $notification->getMessage();
            $additional = $notification->getAdditional();
            if (isset($additional['actions'])) {
                $body['action'] = $additional['actions'];
            }

            try {
                $this->httpClient->request('post',
                    sprintf('api/v1/users/%s/notifications', $recipient->getRemoteId()), [
                        'body' => $body,
                    ]);
            } catch (GuzzleException $e) {
                throw new HandlerException($e);
            }
        }
    }
}