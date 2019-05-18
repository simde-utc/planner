<?php
/**
 * Created by
 * corentinhembise
 * 2019-04-14
 */

namespace App\Form;


use App\Entity\Requirement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequirementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label'  => 'Debut',
            ])
            ->add('endAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label'  => 'Fin',
            ])
            ->add('requirements', IntegerType::class, [
                'attr' => ['min' => 0],
                'label' => 'Besoins',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Requirement::class);
    }
}