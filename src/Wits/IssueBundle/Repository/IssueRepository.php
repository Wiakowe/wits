<?php

namespace Wits\IssueBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Wits\IssueBundle\Entity\Issue;
use Wits\ProjectBundle\Entity\Project;
use Wits\ProjectBundle\Entity\Version;
use Wits\UserBundle\Entity\User;
use Wits\HelperBundle\Util\ArrayUtil;

class IssueRepository extends EntityRepository
{
    public function checkIssueFromProject(Issue $issue, Project $project)
    {
        $query =
            $this->createQueryBuilder('i')
                ->where('i.project = :project')
                ->setParameter('project', $project->getId())
                ->andWhere('i.id = :issue')
                ->setParameter('issue', $issue->getId())
                ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function getIssuesByType(Project $project, Version $version = null, $status_id = null)
    {
        $queryBuilder =
            $this->createQueryBuilder('i')
            ->select('i.status, count(i.status) AS cnt')
            ->andWhere('i.project = :project')
            ->setParameter('project', $project->getId())
            ->groupBy('i.status')
            ->orderBy('i.status')
        ;

        if ($version) {
            $queryBuilder
                ->andWhere('i.version = :version')
                ->setParameter(':version', $version->getId())
            ;
        }

        if ($status_id) {
            $queryBuilder
                ->andWhere('i.status = :status')
                ->setParameter(':status', $status_id)
            ;
        }

        $results = $queryBuilder
            ->getQuery()
            ->getArrayResult();

        return ArrayUtil::setElementAsKeyValue($results, 'status', 'cnt');
    }

    public function getNumberOfIssuesByProject(Project $project, Version $version = null, $status_id = null)
    {
        $queryBuilder =
            $this->createQueryBuilder('i')
            ->select('count(i.id) AS cnt')
            ->where('i.project = :project')
            ->setParameter('project', $project->getId())
        ;

        if ($version) {
            $queryBuilder
                ->andWhere('i.version = :version')
                ->setParameter(':version', $version->getId())
            ;
        }

        if ($status_id) {
            $queryBuilder
                ->andWhere('i.status = :status')
                ->setParameter('status', $status_id)
            ;
        }

        return
            $queryBuilder
            ->getQuery()
            ->getSingleResult();
    }

    public function getLatestIssuesOpened(Project $project, $size = 10)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.status = :status')
            ->setParameter(':status', Issue::STATUS_NEW)
            ->andWhere('i.version IS null')
            ->andWhere('i.project = :project')
            ->setParameter('project', $project->getId())
            ->setMaxResults($size)
            ->orderBy('i.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getCreatedIssues(Project $project, User $user, Version $version = null)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->andWhere('i.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('i.creator = :creator')
            ->setParameter(':creator', $user->getId())
            ;

        if ($version) {
            $queryBuilder
                ->andWhere('i.version = :version')
                ->setParameter(':version', $version->getId())
            ;
        }

        return
            $queryBuilder
                ->getQuery()
                ->getResult();
    }

    public function getAssignedIssues(Project $project, User $user, Version $version = null)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->andWhere('i.project = :project')
            ->setParameter('project', $project->getId())
            ->andWhere('i.assignee = :assignee')
            ->setParameter(':assignee', $user->getId())
        ;

        if ($version) {
            $queryBuilder
                ->andWhere('i.version = :version')
                ->setParameter(':version', $version->getId())
            ;
        }

        return
            $queryBuilder
                ->getQuery()
                ->getResult();
    }
}
