<?php

namespace Wits\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="version")
 * @ORM\Entity()
 */
class Version
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $name;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="version")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     *
     */
    private $project;

    /**
     * @param \Wits\IssueBundle\Entity\Project $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return \Wits\IssueBundle\Entity\Project $project
     */
    public function getProject()
    {
        return $this->project;
    }

}
