<?php
/**
 * Created by
 * corentinhembise
 * 2019-05-18
 */

namespace App\Remote;


use App\Entity\User;
use App\Remote\Entity\Asso;
use Doctrine\Common\Persistence\ObjectRepository;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Serializer\SerializerInterface;
use UnexpectedValueException;

class AssoManager extends RemoteManager
{
    protected function formatGetUrl($id)
    {
        return sprintf("assos/%s", $id);
    }

    protected function formatListUrl()
    {
        return sprintf("assos");
    }

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

    public function findByUserWithPermissions(User $user, $permissionId)
    {
        $request = $this->request('get', sprintf('users/%s/assos', $user->getRemoteId()));
        if ($request->getStatusCode() == 404) {
            return null;
        }
        /** @var Asso[] $assos */
        $assos = $this->serializer->deserialize($request->getBody(), $this->getClassName().'[]', 'json');

        $matchingAssos = [];
        foreach ($assos as $asso) {
            $response = $this->request('get', sprintf('assos/%s/members/%s/permissions/%s',
                $asso->id,
                $user->getRemoteId(),
                $permissionId
            ));
             if ($response->getStatusCode() == 200) {
                 $matchingAssos[] = $asso;
             }
        }

        return $matchingAssos;
    }

    public function findMembersByRoles(string $assoId)
    {
        $response = $this->request('get', sprintf('assos/%s/members', $assoId));
        if ($response->getStatusCode() == 404) {
            return null;
        }

        $membersByRole = [];

        $users = json_decode($response->getBody(), true);
        foreach ($users as $user) {
            //TODO: transform user array to User object
            $roleId = $user['pivot']['role_id'];
            if (!isset($membersByRole[$roleId])) {
                $roleResponse = $this->request('get', sprintf('roles/%s', $roleId));
                $role = json_decode($roleResponse->getBody(), true);

                $membersByRole[$roleId] = $role;
                $membersByRole[$roleId]['members'] = [$user];
            } else {
                $membersByRole[$roleId]['members'][] = $user;
            }
        }

        return $membersByRole;
    }

    /**
     * Returns the class name of the object managed by the repository.
     *
     * @return string
     */
    public function getClassName()
    {
        return Asso::class;
    }
}