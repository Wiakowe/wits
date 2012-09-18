<?php

namespace Wits\IssueBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Wits\IssueBundle\Entity\Issue;
use Wits\ProjectBundle\Entity\Project;
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

    public function getIssuesByType(Project $project)
    {
        $results =  $this->createQueryBuilder('i')
            ->select('i.status, count(i.status) AS cnt')
            ->where('i.project = :project')
            ->setParameter('project', $project->getId())
            ->groupBy('i.status')
            ->orderBy('i.status')
            ->getQuery()
            ->getArrayResult();

        return ArrayUtil::setElementAsKeyValue($results, 'status', 'cnt');
    }

    public function getNumberOfIssuesByProject(Project $project)
    {
        return $this->createQueryBuilder('i')
            ->select('count(i.id) AS cnt')
            ->where('i.project = :project')
            ->setParameter('project', $project->getId())
            ->getQuery()
            ->getSingleResult();
    }
}
