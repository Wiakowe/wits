<?php

namespace Wits\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wits\IssueBundle\Entity\Issue;
use Wits\ProjectBundle\Entity\Project;
use Wits\IssueBundle\Entity\Comment;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CommentController extends Controller
{
    public function createAction(Project $project, Issue $issue)
    {
        $issueRepository = $this->getDoctrine()->getRepository('WitsIssueBundle:Issue');
        if (!$issueRepository->checkIssueFromProject($issue, $project)) {
            throw new ResourceNotFoundException();
        }

        $comment = new Comment();

        $form = $this->createFormBuilder($comment)
            ->add('comment')
            ->getForm()
        ;

        if ($this->getRequest()->getMethod() == 'POST') {

            $form->bind($this->getRequest());

            if ($form->isValid()) {

                $manager = $this->getDoctrine()->getManager();

                $comment->setIssue($issue);
                $comment->setUser($this->getUser());

                $manager->persist($comment);
                $manager->flush();

                $this->getRequest()->getSession()->getFlashBag()->add('success', 'Your comment has been added to the issue');

                return $this->redirect($this->get('router')->generate('wits_issue_show', array('project_id' => $project->getId(), 'issue_id' => $issue->getId())));
            }
        }

        return $this->render('WitsIssueBundle:Issue:edit.html.twig',
            array(
                'form'  => $form->createView()
            )
        );
    }


}
