<?php

namespace Wits\IssueBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Wits\IssueBundle\Entity\Issue;
use Wits\ProjectBundle\Entity\Project;
use Wits\ProjectBundle\Entity\Version;
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

    public function getIssuesByType(Project $project, Version $version = null)
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

        $results = $queryBuilder
            ->getQuery()
            ->getArrayResult();

        return ArrayUtil::setElementAsKeyValue($results, 'status', 'cnt');
    }

    public function getNumberOfIssuesByProject(Project $project, Version $version = null)
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

        return
            $queryBuilder
            ->getQuery()
            ->getSingleResult();
    }

    public function getLatestIssuesOpened(Project $project, $size = 10)
    {
        return $this->createQueryBuilder('i')
            ->where('i.status = :status')
            ->setParameter(':status', Issue::STATUS_NEW)
            ->setMaxResults($size)
            ->orderBy('i.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
