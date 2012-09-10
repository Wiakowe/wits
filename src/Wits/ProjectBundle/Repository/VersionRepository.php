<?php

namespace Wits\ProjectBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Wits\ProjectBundle\Entity\Version;
use Wits\ProjectBundle\Entity\Project;

class VersionRepository extends EntityRepository
{
    public function checkVersionFromProject(Version $version, Project $project)
    {
        $query =
            $this->createQueryBuilder('v')
                ->where('v.project = :project')
                ->setParameter('project', $project->getId())
                ->andWhere('v.id = :version')
                ->setParameter('version', $version->getId())
                ->getQuery();

        return $query->getOneOrNullResult();
    }
}
