<?php

namespace Wits\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wits\IssueBundle\Entity\Issue;
use Wits\ProjectBundle\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class IssueController extends Controller
{
    public function editAction(Project $project, Issue $issue = null)
    {
        $isEdit = (boolean) $issue;

        if (!$isEdit)  {
            $issue = new Issue();
        }

        $form = $this->createFormBuilder($issue)
            ->add('name')
            ->add('description')
            ->add('version', null, array('required' => false))
            ->getForm()
        ;

        if ($this->getRequest()->getMethod() == 'POST') {

            $form->bind($this->getRequest());

            if ($form->isValid()) {

                $manager = $this->getDoctrine()->getManager();

                $issue->setProject($project);
                $issue->setCreator($this->getUser());

                $manager->persist($issue);
                $manager->flush();

                $this->getRequest()->getSession()->getFlashBag()->add('success', ($isEdit) ? 'Your issue has been edited' : 'Your issue has been created');

                return $this->redirect($this->get('router')->generate('wits_issue_show', array('project_id' => $project->getId(), 'issue_id' => $issue->getId())));
            }
        }

        return $this->render('WitsIssueBundle:Issue:edit.html.twig',
            array(
                'form'  => $form->createView()
            )
        );
    }

    public function showAction(Project $project, Issue $issue)
    {
        $commentRepository = $this->getDoctrine()->getRepository('WitsIssueBundle:Comment');

        $comments = $commentRepository->findBy(array('issue' => $issue->getId()));

        return $this->render('WitsIssueBundle:Issue:show.html.twig',
            array(
                'project'   => $project,
                'issue'     => $issue,
                'comments'  => $comments
            )
        );
    }
}
