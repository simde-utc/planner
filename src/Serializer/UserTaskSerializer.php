<?php
/**
 * Created by
 * corentinhembise
 * 2019-06-12
 */

namespace App\Serializer;


use App\Entity\UserTask;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UserTaskSerializer implements NormalizerInterface
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
     * @param UserTask $userTask Object to normalize
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
    public function normalize($userTask, $format = null, array $context = [])
    {
        $context = array_merge($context, [
            'ignored_attributes' => [
                'user', 'task',
            ],
        ]);

        $data = $this->normalizer->normalize($userTask, $format, $context);

        $data['title'] = $userTask->getTask()->getName();
        $data['description'] = $userTask->getTask()->getDescription();
        $data['start'] = $data['startAt'];
        $data['end'] = $data['endAt'];
        $data['resourceId'] = $userTask->getUser()->getId();
        $data['backgroundColor'] = $userTask->getTask()->getColor();

        // constraint this task based on skills
        $skills = $userTask->getTask()->getSkills();

        $resourceIds = [];
        foreach ($skills as $skill) {
            foreach ($skill->getUsers() as $user) {
                if (!in_array($user->getId(), $resourceIds)) {
                    $resourceIds[] = $user->getId();
                }
            }
        }

        if (!empty($resourceIds)) {
            $data['constraint']['resourceIds'] = $resourceIds;
        }

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
        return $data instanceof UserTask;
    }
}