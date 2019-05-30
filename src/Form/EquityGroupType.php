<?php
/**
 * Created by
 * corentinhembise
 * 2019-05-24
 */

namespace App\Form;


use App\Entity\EquityGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquityGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => "Nom",
            ])
            ->add('workingLimit', TimeType::class, [
                'label' => "Limite de charge de travail",
                'help'  => "Charge de travail maximale par tranche de 24h",
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EquityGroup::class,
        ]);
    }
}