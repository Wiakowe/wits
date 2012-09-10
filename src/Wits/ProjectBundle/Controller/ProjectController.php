<?php

namespace Wits\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wits\ProjectBundle\Entity\Project;

class ProjectController extends Controller
{
    public function dashboardAction()
    {
        $projectRepository = $this->getDoctrine()->getRepository('WitsProjectBundle:Project');

        $projects = $projectRepository->findAll();

        if (count($projects) == 0) {
            return $this->redirect($this->get('router')->generate('wits_project_new'));
        } else {
            $project = $projects[0];
            return $this->redirect($this->get('router')->generate('wits_project_show', array('id' => $project->getId())));
        }
    }

    public function showAction(Project $project)
    {
        return $this->render('WitsProjectBundle:Project:show.html.twig',
            array(
                'project' => $project
            )
        );
    }

    public function editAction(Project $project = null)
    {
        $isEdit = (boolean) $project;

        if (!$isEdit)  {
            $project = new Project();
        }

        $form = $this->createFormBuilder($project)
            ->add('name')
            ->getForm()
        ;

        if ($this->getRequest()->getMethod() == 'POST') {

            $form->bind($this->getRequest());

            if ($form->isValid()) {

                $manager = $this->getDoctrine()->getManager();

                $manager->persist($project);
                $manager->flush();


                $this->getRequest()->getSession()->getFlashBag()->add('success', ($isEdit) ? 'Your project has been edited' : 'Your project has been created');

                return $this->redirect($this->get('router')->generate('wits_project_dashboard'));
            }
        }

        return $this->render('WitsProjectBundle:Project:edit.html.twig',
            array(
                'form'  => $form->createView()
            )
        );
    }
}
