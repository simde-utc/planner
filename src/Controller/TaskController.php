<?php
/**
 * Created by
 * corentinhembise
 * 2019-03-26
 */

namespace App\Controller;


use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TaskController extends AbstractController
{
    public function tasks()
    {
        return $this->render('event/tasks/index.html.twig');
    }

    public function edit()
    {
        $task = new Task();
        $task
            ->setName("Vérification billet et encodage bracelet")
            ->setDescription("Vérification du billet des festivaliers, encodage du bracelet à puce en fonction de leur billet (1 jour/2 jours/camping) avec les scans Weezevent mis à votre disposition, scanner les QR code des billets des festivaliers et distribution bracelet camping pour les campeurs (Bicolore pour 2 jours + camping et une couleur pour 1 jour+camping).\nUne personne de la team en vérification des billets VIP, presse, bénévole. Distribution des bracelets presse, accès crash, accès total et les diriger vers le point accueil. (+ badge et bracelet si accès total).\nDistribution de cendriers de poche.")
            ->setLocation("Entrées, dans la file vérification des billets")
            ->setColor("#d32f2f")
        ;
        $form = $this->createForm(TaskType::class, $task);
        $form->add('submit', SubmitType::class, [
            'label' => 'Enregistrer',
        ]);

        return $this->render('event/tasks/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}