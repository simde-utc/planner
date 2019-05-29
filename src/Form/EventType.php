<?php
/**
 * Created by PhpStorm.
 * User: corentinhembise
 * Date: 2019-03-19
 * Time: 10:36
 */

namespace App\Form;


use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('startAt', null, [
                'label' => 'Commence à',
                'widget' => 'single_text',
            ])
            ->add('endAt', null, [
                'label' => 'Termine à',
                'widget' => 'single_text',
            ])
            ->add('allowSubmissions', CheckboxType::class, [
                'label' => "Accepter les inscriptions à cet évènement",
                'help'  => "Tous les utilisateur·rice·s de la plateforme pourrons candidater pour participer à cet évènement",
                "required" => false,
            ])
        ;
        if ($options['organizations']) {
            $builder->add('remoteOrganizationId', ChoiceType::class, [
                'label' => "Association",
                'choices' => $options['organizations'],
                'choice_label' => function ($choice, $key, $value) {
                    return $choice->shortname;
                },
                'choice_value' => function ($entity = null) {
                    if ($entity)
                        return $entity->id;
                },
            ]);
        }
        if ($options['from_base']) {
            $builder->add('fromBase', ChoiceType::class, [
                'mapped' => false
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'from_base'  => false,
            'organizations' => null,
        ]);
    }
}