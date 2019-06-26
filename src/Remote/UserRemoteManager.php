<?php
/**
 * Created by
 * corentinhembise
 * 2019-05-30
 */

namespace App\Remote;


use App\Remote\Entity\User;
use GuzzleHttp\Exception\RequestException;

class UserRemoteManager extends RemoteManager
{

    /**
     * Finds a single object by a set of criteria.
     *
     * @param mixed[] $criteria The criteria.
     *
     * @return object|null The object.
     */
    public function findOneBy(array $criteria)
    {
        // TODO: Implement findOneBy() method.
    }

    public function findContactFor($userId)
    {
        try {
            $response = $this->request('get', sprintf('users/%s/contacts', $userId));
        } catch (RequestException $e) {
            return [];
        }

        $contacts = json_decode($response->getBody(), true);

        return $contacts;
    }

    public function search(string $query)
    {
        $request = $this->request('get', "users", [
            'query' => ['search' => $query, ]
        ]);
        if ($request->getStatusCode() == 404 || $request->getStatusCode() == 416) {
            return null;
        }

        $objects = $this->serializer->deserialize($request->getBody(), $this->getClassName()."[]", 'json');

        return $objects;
    }

    /**
     * @param $assoId
     * @return User[]
     */
    public function findUsersForAsso($assoId)
    {
        $request = $this->request('get', sprintf("assos/%s/members", $assoId));
        if ($request->getStatusCode() == 404 || $request->getStatusCode() == 416) {
            return null;
        }

        $objects = $this->serializer->deserialize($request->getBody(), $this->getClassName()."[]", 'json');

        return $objects;
    }

    /**
     * Returns the class name of the object managed by the repository.
     *
     * @return string
     */
    public function getClassName()
    {
        return User::class;
    }

    protected function formatGetUrl($id)
    {
        return sprintf("users/%s", $id);
    }

    protected function formatListUrl()
    {
        return sprintf("users");
    }
}