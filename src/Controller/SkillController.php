<?php
/**
 * Created by
 * corentinhembise
 * 2019-04-09
 */

namespace App\Controller;


use App\Entity\Event;
use App\Entity\Skill;
use App\Form\SkillType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * TODO: refactor with task controller
 */
class SkillController extends AbstractController
{
    public function index(Event $event)
    {
        $skills = $event->getSkills();
        return $this->render('event/skills/show.html.twig', [
            'event' => $event,
            'skill' => $skills ? $skills->first() : null,
        ]);
    }

    public function new(Event $event, Request $request)
    {
        $skill = new Skill();
        $skill->setEvent($event);

        $form = $this->createForm(SkillType::class, $skill);
        $form->add('submit', SubmitType::class, [
            'label' => 'Créer',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($skill);
            $em->flush();

            return $this->redirectToRoute('event_skills_show', [
                'id' => $event->getId(),
                'skill_id' => $skill->getId(),
            ]);
        }

        return $this->render('event/skills/new.html.twig', [
            'form'  => $form->createView(),
            'event' => $event,
        ]);
    }

    /**
     * @ParamConverter("skill", class="App\Entity\Skill",  options={"mapping": {"skill_id": "id"}})
     */
    public function show(Event $event, Skill $skill)
    {
        return $this->render('event/skills/show.html.twig', [
            'skill'  => $skill,
            'event' => $event,
        ]);
    }

    /**
     * @ParamConverter("skill", class="App\Entity\Skill",  options={"mapping": {"skill_id": "id"}})
     */
    public function edit(Event $event, Skill $skill, Request $request)
    {
        $form = $this->createForm(SkillType::class, $skill, [
            'event' => $event,
        ]);
        $form->add('submit', SubmitType::class, [
            'label' => 'Enregistrer',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($skill);
            $em->flush();

            return $this->redirectToRoute('event_skills_show', [
                'id' => $event->getId(),
                'skill_id' => $skill->getId(),
            ]);
        }

        return $this->render('event/skills/edit.html.twig', [
            'form'  => $form->createView(),
            'event' => $event,
            'skill'  => $skill,
        ]);
    }

    /**
     * @ParamConverter("skill", class="App\Entity\Skill",  options={"mapping": {"skill_id": "id"}})
     */
    public function delete(Event $event, Skill $skill)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($skill);
        $em->flush();

        return $this->redirectToRoute('event_skills', [
            'id' => $event->getId(),
        ]);
    }
}