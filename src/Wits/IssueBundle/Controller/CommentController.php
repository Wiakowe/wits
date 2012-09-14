<?php

namespace Wits\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wits\IssueBundle\Entity\Issue;
use Wits\ProjectBundle\Entity\Project;
use Wits\IssueBundle\Entity\Comment;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Wits\IssueBundle\Event\IssueCreateEvent;
use Wits\IssueBundle\Event\IssueEvents;

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

        $breadcrumb = $this->get('wiakowe.breadcrumb');
        $breadcrumb->addEntry('label_issues', 'wits_issue_list', array('project_id' => $project->getId()));
        $breadcrumb->addEntry($issue->getName(), 'wits_issue_show', array('project_id' => $project->getId(), 'issue_id' => $issue->getId()));
        $breadcrumb->addEntry('label_comment', 'wits_comment_create', array('project_id' => $project->getId(), 'issue_id' => $issue->getId()));

        $form = $this->createFormBuilder($comment)
            ->add('comment', 'textarea', array('label' => 'label_comment'))
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

                //dispatch the event
                $event = new IssueCreateCommentEvent($comment, $issue);
                $dispatcher = $this->get('event_dispatcher');
                $dispatcher->dispatch(IssueEvents::ISSUE_COMMENT, $event);


                $this->getRequest()->getSession()->getFlashBag()->add('success', 'label_comment_created');

                return $this->redirect($this->get('router')->generate('wits_issue_show', array('project_id' => $project->getId(), 'issue_id' => $issue->getId())));
            }
        }

        return $this->render('WitsIssueBundle:Comment:edit.html.twig',
            array(
                'issue'     => $issue,
                'project'   => $project,
                'form'      => $form->createView()
            )
        );
    }


}
