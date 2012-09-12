<?php

namespace Wits\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wits\IssueBundle\Entity\Issue;
use Wits\ProjectBundle\Entity\Project;
use Wits\IssueBundle\Entity\Comment;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CommentController extends Controller
{
    public function createAction(Project $project, Issue $issue)
    {

        if (false === $this->get('security.context')->isGranted('ROLE_ISSUE_COMMENT')) {
            throw new AccessDeniedException();
        }

        $issueRepository = $this->getDoctrine()->getRepository('WitsIssueBundle:Issue');
        if (!$issueRepository->checkIssueFromProject($issue, $project)) {
            throw new ResourceNotFoundException();
        }

        $comment = new Comment();

        $breadcrumb = $this->get('wits.breadcrumb');
        $breadcrumb->addEntry('Issues', 'wits_issue_list', array('project_id' => $project->getId()));
        $breadcrumb->addEntry($issue->getName(), 'wits_issue_show', array('project_id' => $project->getId(), 'issue_id' => $issue->getId()));
        $breadcrumb->addEntry('Comentar', 'wits_comment_create', array('project_id' => $project->getId(), 'issue_id' => $issue->getId()));

        $form = $this->createFormBuilder($comment)
            ->add('comment', 'textarea')
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

        return $this->render('WitsIssueBundle:Comment:edit.html.twig',
            array(
                'form'  => $form->createView()
            )
        );
    }


}
