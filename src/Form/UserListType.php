<?php
/**
 * Created by
 * corentinhembise
 * 2019-05-29
 */

namespace App\Form;


use App\Entity\Availability;
use App\Repository\AvailabilityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserListType extends AbstractType
{
    /**
     * @var AvailabilityRepository
     */
    private $availabilityRepository;

    public function __construct(AvailabilityRepository $availabilityRepository)
    {
        $this->availabilityRepository = $availabilityRepository;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('multiple', true);
        $resolver->setDefault('class', Availability::class);
        $resolver->setDefault('choice_label', 'user');

    }

    public function getParent()
    {
        return EntityType::class;
    }
}