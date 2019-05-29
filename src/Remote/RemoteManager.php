<?php
/**
 * Created by
 * corentinhembise
 * 2019-05-18
 */

namespace App\Remote;


use Doctrine\Common\Persistence\ObjectRepository;
use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use UnexpectedValueException;

abstract class RemoteManager implements ObjectRepository
{
    /**
     * @var ClientInterface
     */
    protected $client;
    /**
     * @var SerializerInterface
     */
    protected $serializer;
    /**
     * @var string
     */
    protected $accessToken;

    public function __construct(ClientInterface $client, SerializerInterface $serializer, SessionInterface $session)
    {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->accessToken = $session->get('access_token');
    }

    public function find($id)
    {
        $request = $this->request('get', $this->formatGetUrl($id));
        if ($request->getStatusCode() == 404) {
            return null;
        }

        $object = $this->serializer->deserialize($request->getBody(), $this->getClassName(), 'json');

        return $object;
    }

    /**
     * Finds all objects in the repository.
     *
     * @return object[] The objects.
     */
    public function findAll()
    {
        $request = $this->request('get', $this->formatListUrl());
        if ($request->getStatusCode() == 404) {
            return null;
        }

        $objects = $this->serializer->deserialize($request->getBody(), $this->getClassName()."[]", 'json');

        return $objects;
    }


    /**
     * Finds objects by a set of criteria.
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw
     * an UnexpectedValueException if certain values of the sorting or limiting details are
     * not supported.
     *
     * @param mixed[] $criteria
     * @param string[]|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return object[] The objects.
     *
     * @throws UnexpectedValueException
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        $request = $this->request('get', $this->formatListUrl(), [
            'query' => $criteria,
        ]);
        if ($request->getStatusCode() == 404) {
            return null;
        }

        $objects = $this->serializer->deserialize($request->getBody(), $this->getClassName()."[]", 'json');

        return $objects;
    }

    protected abstract function formatGetUrl($id);

    protected abstract function formatListUrl();

    protected function request($method, $uri, array $options = [])
    {
        $headers = $options['headers'] ?? [];

        $options['headers'] = array_merge($headers, [
            'Authorization' => 'Bearer ' . $this->accessToken
        ]);

        return $this->client->request($method, $uri, $options);
    }
}