<?php

namespace Wits\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wits\ProjectBundle\Entity\Project;
use Wits\IssueBundle\Entity\Issue;
use Wits\ProjectBundle\Entity\Version;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class VersionController extends Controller
{
    public function editAction(Project $project, Version $version = null)
    {
        $isEdit = (boolean) $version;

        if (!$isEdit)  {
            if (false === $this->get('security.context')->isGranted('ROLE_VERSION_EDIT')) {
                throw new AccessDeniedException();
            }
            $version = new Version();
        } else {
            if (false === $this->get('security.context')->isGranted('ROLE_VERSION_CREATE')) {
                throw new AccessDeniedException();
            }
            $versionRepository = $this->getDoctrine()->getRepository('WitsProjectBundle:Version');
            if (!$versionRepository->checkVersionFromProject($version, $project)) {
                throw new ResourceNotFoundException();
            }
        }

        $form = $this->createFormBuilder($version)
            ->add('name')
            ->getForm()
        ;

        if ($this->getRequest()->getMethod() == 'POST') {

            $form->bind($this->getRequest());

            if ($form->isValid()) {

                $manager = $this->getDoctrine()->getManager();

                $version->setProject($project);

                $manager->persist($version);
                $manager->flush();

                $this->getRequest()->getSession()->getFlashBag()->add('success', ($isEdit) ? 'Your version has been edited' : 'Your version has been created');

                return $this->redirect($this->get('router')->generate('wits_version_show', array('project_id' => $project->getId(), 'version_id' => $version->getId())));
            }
        }

        return $this->render('WitsProjectBundle:Version:edit.html.twig',
            array(
                'form'  => $form->createView()
            )
        );
    }

    public function listAction(Project $project)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_VERSION_LIST')) {
            throw new AccessDeniedException();
        }

        $versionRepository = $this->getDoctrine()->getRepository('WitsProjectBundle:Version');

        $versions = $versionRepository->findBy(array('project' => $project->getId()));

        return $this->render('WitsProjectBundle:Version:list.html.twig',
            array(
                'project'   => $project,
                'versions'  => $versions
            )
        );
    }

    public function showAction(Project $project, Version $version)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_VERSION_SHOW')) {
            throw new AccessDeniedException();
        }

        $versionRepository = $this->getDoctrine()->getRepository('WitsProjectBundle:Version');
        if (!$versionRepository->checkVersionFromProject($version, $project)) {
            throw new ResourceNotFoundException();
        }

        $issueRepository = $this->getDoctrine()->getRepository('WitsIssueBundle:Issue');

        $issues = $issueRepository->findAll(array('version' => $version));

        return $this->render('WitsProjectBundle:Version:show.html.twig',
            array(
                'project'   => $project,
                'version'   => $version,
                'issues'    => $issues
            )
        );
    }
}
