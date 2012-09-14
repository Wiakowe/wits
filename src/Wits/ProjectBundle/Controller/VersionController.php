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


        $breadcrumb = $this->get('wits.breadcrumb');
        $breadcrumb->addEntry($project->getName(), 'wits_project_show', array('id' => $project->getId()));
        $breadcrumb->addEntry('label_versions', 'wits_version_list', array('project_id' => $project->getId()));


        if (!$isEdit)  {
            if (false === $this->get('security.context')->isGranted('ROLE_VERSION_EDIT')) {
                throw new AccessDeniedException();
            }
            $version = new Version();

            $breadcrumb->addEntry('label_create', 'wits_version_new', array('project_id' => $project->getId()));


        } else {
            if (false === $this->get('security.context')->isGranted('ROLE_VERSION_CREATE')) {
                throw new AccessDeniedException();
            }
            $versionRepository = $this->getDoctrine()->getRepository('WitsProjectBundle:Version');
            if (!$versionRepository->checkVersionFromProject($version, $project)) {
                throw new ResourceNotFoundException();
            }

            $breadcrumb->addEntry($version->getName(), 'wits_version_show', array('project_id' => $project->getId(), 'version_id' => $version->getId()));
            $breadcrumb->addEntry('label_edit', 'wits_version_edit', array('project_id' => $project->getId(), 'version_id' => $version->getId()));

        }

        $form = $this->createFormBuilder($version)
            ->add('name', null, array('label' => 'label_version_name'))
            ->getForm()
        ;

        if ($this->getRequest()->getMethod() == 'POST') {

            $form->bind($this->getRequest());

            if ($form->isValid()) {

                $manager = $this->getDoctrine()->getManager();

                $version->setProject($project);

                $manager->persist($version);
                $manager->flush();

                $this->getRequest()->getSession()->getFlashBag()->add('success', ($isEdit) ? 'label_version_edited' : 'label_version_created');

                return $this->redirect($this->get('router')->generate('wits_version_show', array('project_id' => $project->getId(), 'version_id' => $version->getId())));
            }
        }

        return $this->render('WitsProjectBundle:Version:edit.html.twig',
            array(
                'project'   => $project,
                'version'   => $version,
                'form'      => $form->createView()
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

        $breadcrumb = $this->get('wits.breadcrumb');
        $breadcrumb->addEntry($project->getName(), 'wits_project_show', array('id' => $project->getId()));
        $breadcrumb->addEntry('label_versions', 'wits_version_list', array('project_id' => $project->getId()));

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

        $breadcrumb = $this->get('wits.breadcrumb');
        $breadcrumb->addEntry($project->getName(), 'wits_project_show', array('id' => $project->getId()));
        $breadcrumb->addEntry('label_versions', 'wits_version_list', array('project_id' => $project->getId()));
        $breadcrumb->addEntry($version->getName(), 'wits_version_show', array('project_id' => $project->getId(), 'version_id' => $version->getId()));

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
