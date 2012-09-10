<?php

namespace Wits\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Wits\UserBundle\Entity\User;
use Wits\ProjectBundle\Entity\Project;


/**
 * @ORM\Table(name="issue")
 * @ORM\Entity()
 */
class Issue
{
    const   STATUS_NEW          = 1,
            STATUS_ASSIGNED     = 2,
            STATUS_WORKING      = 3,
            STATUS_RESOLVED     = 4,
            STATUS_CLOSED       = 5;

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
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=4096)
     */
    protected $description;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Wits\ProjectBundle\Entity\Project", inversedBy="issues")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     *
     */
    protected $project;

    /**
     * @var \Wits\ProjectBundle\Entity\Version
     *
     * @ORM\ManyToOne(targetEntity="Wits\ProjectBundle\Entity\Version", inversedBy="issues")
     * @ORM\JoinColumn(name="version_id", referencedColumnName="id")
     *
     */
    protected $version;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $status;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Wits\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     *
     */
    protected $creator;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Wits\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="assignee_id", referencedColumnName="id")
     *
     */
    protected $assignee;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(
     *          targetEntity="Comment",
     *          mappedBy="issue"
     * )
     */
    private $comments;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

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

    /**
     * @param \Wits\UserBundle\Entity\User $assignee
     */
    public function setAssignee($assignee)
    {
        $this->assignee = $assignee;
    }

    /**
     * @return \Wits\UserBundle\Entity\User
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * @param \Wits\UserBundle\Entity\User $creator
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * @return \Wits\UserBundle\Entity\User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }
}
