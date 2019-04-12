<?php

namespace App\Form;

use App\Entity\Skill;
use App\Entity\Task;
use App\Repository\SkillRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('skills', EntityType::class, [
                'label' => 'Compétences requises',
                'help'  => "Aucune restriction de compétence ne sera appliqué si rien n'est selectionné.",
                'class' => Skill::class,
                'multiple' => true,
                'by_reference' => false,
                'query_builder' => function(SkillRepository $er) use ($builder) {
                    /** @var Task $task */
                    $task = $builder->getData();
                    return $er->findAllForEvent($task->getEvent());
                }
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
