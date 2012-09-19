<?php

namespace Wits\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Wits\UserBundle\Entity\User;
use Wits\ProjectBundle\Entity\Project;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="issue")
 * @ORM\Entity(repositoryClass="Wits\IssueBundle\Repository\IssueRepository")
 */
class Issue
{
    const   STATUS_NEW          = 1,
            STATUS_ASSIGNED     = 2,
            STATUS_WORKING      = 3,
            STATUS_RESOLVED     = 4,
            STATUS_CLOSED       = 5;

    public static $statusList = array(
        self::STATUS_NEW        => 'label_issue_status_new',
        self::STATUS_ASSIGNED   => 'label_issue_status_assigned',
        self::STATUS_WORKING    => 'label_issue_status_working',
        self::STATUS_RESOLVED   => 'label_issue_status_resolved',
        self::STATUS_CLOSED     => 'label_issue_status_closed',
    );

    const   PRIORITY_LOW        = 1,
            PRIORITY_MEDIUM     = 2,
            PRIORITY_HIGH       = 3,
            PRIORITY_CRITICAL   = 4;

    public static $priorityList = array(
        self::PRIORITY_LOW        => 'label_issue_priority_low',
        self::PRIORITY_MEDIUM     => 'label_issue_priority_medium',
        self::PRIORITY_HIGH       => 'label_issue_priority_high',
        self::PRIORITY_CRITICAL   => 'label_issue_priority_critical',
    );

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
     * @ORM\Column(type="string", length=4096, nullable=true)
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
    protected $status = self::STATUS_NEW;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $priority = self::PRIORITY_MEDIUM;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Wits\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=false)
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
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="timestamp")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="timestamp", nullable=true, columnDefinition="TIMESTAMP NULL")
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->status = self::STATUS_NEW;
        $this->priority = self::PRIORITY_MEDIUM;
    }

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
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
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

    /**
     * @param \Wits\ProjectBundle\Entity\Version $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return \Wits\ProjectBundle\Entity\Version
     */
    public function getVersion()
    {
        return $this->version;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getStatusName()
    {
        if (array_key_exists($this->getStatus(), self::$statusList)) {
            return self::$statusList[$this->getStatus()];
        }
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    public function getPriorityName()
    {
        if (array_key_exists($this->getPriority(), self::$priorityList)) {
            return self::$priorityList[$this->getPriority()];
        }
    }

}
