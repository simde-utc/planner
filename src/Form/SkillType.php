<?php
/**
 * Created by
 * corentinhembise
 * 2019-04-12
 */

namespace App\Form;


use App\Entity\Event;
use App\Entity\Skill;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('askOnSubscribe', CheckboxType::class, [
                'label' => "Demander lors de l'inscription",
                "help"  => "L'utilisateur·rice devra renseigner cette compétence lors de son inscription.",
                'required' => false,
            ])
            ->add('users', EntityType::class, [
                'label' => "Affecter les compétences aux utilisateur·rice·s",
                'class' => User::class,
                'multiple' => true,
                'by_reference' => false,
                'query_builder' => function(UserRepository $er) use ($builder) {
                    return $er->getUsersForEvent($builder->getOption('event'));
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Skill::class,
        ]);

        $resolver->setRequired('event');
        $resolver->setAllowedTypes('event', Event::class);
    }
}