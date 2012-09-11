<?php

namespace Wits\IssueBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Wits\IssueBundle\Entity\Issue;
use Wits\ProjectBundle\Entity\Project;

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
}
