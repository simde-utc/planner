<?php
/**
 * Created by
 * corentinhembise
 * 2019-06-04
 */

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class InvitationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('users', Select2EntityType::class, [
                'label' => 'Selectionner des utilisateur·ice·s',
                'help' => "Il n'est possible d'inviter que des utilisateurs disposant un compte sur le portail des assos.",
                'remote_route' => 'index',
                'placeholder' => 'Rechercher un utilisateur·ice·s du portail',
                'multiple' => true,
                'delay' => 3000,
                'minimum_input_length' => 2, //TODO: see why it doesn't work
                'text_property' => 'name',
            ])
            ->add('message', TextareaType::class, [
                'label' => "Message d'invitation",
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Inviter',
            ])
        ;
    }
}