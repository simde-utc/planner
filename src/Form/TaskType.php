<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Skill;
use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Titre',
            ])
            ->add('location', null, [
                'label' => 'Lieu',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('minWorkingTime', TimeType::class, [
                'label'   => 'Temps de travail minimum sur la tâche',
                'minutes' => $this->getMinutesAsArray($options['precision']),
                'hours'   => $this->getHoursAsArray($options['precision']),
                'data' => new \DateTime("01:00:00"),
            ])
            ->add('maxWorkingTime', TimeType::class, [
                'label'   => 'Temps de travail maximum sur la tâche',
                'minutes' => $this->getMinutesAsArray($options['precision']),
                'hours'   => $this->getHoursAsArray($options['precision']),
                'data' => new \DateTime("03:00:00"),
            ])
            ->add('skills', Select2EntityType::class, [
                'label' => 'Compétences requises',
                'help'  => "Seul les utilisateur·rice·s avec ces compétences pourront effectuer cette tâche.",
                'class' => Skill::class,
                'multiple' => true,
                'by_reference' => false,
                'remote_route' => 'event_skills_api_list',
                'remote_params' => [
                    'id' => $builder->getOption('event')->getId(),
                ],
                /*
                'query_builder' => function(SkillRepository $er) use ($builder) {
                    /** @var Task $task *
                    $task = $builder->getData();
                    return $er->findAllForEvent($task->getEvent());
                }
                */
            ])
            ->add('color', ColorType::class, [
                'label' => 'Couleur',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'precision'  => null,
        ]);

        $resolver->setRequired('event');
        $resolver->setAllowedTypes('event', Event::class);
    }

    private function getMinutesAsArray(\DateTime $precision = null): array
    {
        $precision = $precision ? intval($precision->format('i')) : 15;
        if ($precision > 0) {
            $minutes = range(0, 59, $precision);
        } else {
            $minutes = [0];
        }

        return $minutes;
    }

    private function getHoursAsArray(\DateTime $precision = null, \DateInterval $duration = null): array
    {
        $precision = $precision ? intval($precision->format('H')) : 0;
        $duration = $duration ? intval($duration->format('H')) : 23;

        $beginAt = $precision > 0 ? 1 : 0;
        $endAt = min(23, $duration);

        $hours = range($beginAt, $endAt, 1);


        return $hours;
    }
}
