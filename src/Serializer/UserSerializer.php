<?php
/**
 * Created by
 * corentinhembise
 * 2019-06-12
 */

namespace App\Serializer;


use App\Entity\User;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UserSerializer implements NormalizerInterface
{
    /**
     * @var ObjectNormalizer
     */
    private $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param User $user Object to normalize
     * @param string $format Format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array|string|int|float|bool
     *
     * @throws InvalidArgumentException   Occurs when the object given is not an attempted type for the normalizer
     * @throws CircularReferenceException Occurs when the normalizer detects a circular reference when no circular
     *                                    reference handler can fix it
     * @throws LogicException             Occurs when the normalizer is not called in an expected context
     * @throws ExceptionInterface         Occurs for all the other cases of errors
     */
    public function normalize($user, $format = null, array $context = [])
    {
        $context = array_merge($context, [
            'ignored_attributes' => [
                'userTasks', 'availabilities', 'eventRequests', 'roles',
            ],
        ]);
        $data = $this->normalizer->normalize($user, $format, $context);

        // Here, add, edit, or delete some data:
        $data['title'] = $user->getName();
        $data['eventConstraint']['startTime'] = "2019-05-12T20:00:00";
        $data['eventConstraint']['endTime'] = "2019-05-13T09:00:00";

        unset($data['remoteId'], $data['lastLogin'], $data['password'], $data['salt']);

        return $data;
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data Data to normalize
     * @param string $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof User;
    }
}