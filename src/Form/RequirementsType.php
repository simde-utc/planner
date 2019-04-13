<?php
/**
 * Created by
 * corentinhembise
 * 2019-04-13
 */

namespace App\Form;

use App\Entity\Requirement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequirementsType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('entry_type', EntityType::class)
            ->setDefault('entry_options', [
                'class' => Requirement::class
            ])
            ->setDefault('allow_add', true)
            ->setDefault('allow_delete', true)
            ->setDefault('delete_empty', true)
            ->setDefault('block_name', 'requirements')

            ->setRequired(['startAt', 'endAt'])
            ->setAllowedTypes('startAt', \DateTime::class)
            ->setAllowedTypes('endAt', \DateTime::class)
        ;
    }

    public function getParent()
    {
        return CollectionType::class;
    }
}