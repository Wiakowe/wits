<?php

namespace Wits\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Wits\ProjectBundle\Entity\Project;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Table(name="version")
 * @ORM\Entity(repositoryClass="Wits\ProjectBundle\Repository\VersionRepository")
 */
class Version
{
    const   STATUS_NEW          = 1,
            STATUS_WORKING      = 2,
            STATUS_RELEASED     = 3;

    public static $statusList = array(
        self::STATUS_NEW        => 'label_issue_status_new',
        self::STATUS_WORKING    => 'label_issue_status_working',
        self::STATUS_RELEASED   => 'label_issue_status_released',
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
     * @var \Date
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $dateStart;

    /**
     * @var \Date
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $dateEnd;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $status = self::STATUS_NEW;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Wits\ProjectBundle\Entity\Project", inversedBy="version")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     *
     */
    private $project;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(
     *          targetEntity="Wits\IssueBundle\Entity\Issue",
     *          mappedBy="version"
     * )
     */
    private $issues;

    public function __construct()
    {
        $this->issues = new ArrayCollection();
        $this->status = self::STATUS_NEW;
    }

    /**
     * @param Project $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return Project $project
     */
    public function getProject()
    {
        return $this->project;
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @param \DateTime $dateEnd
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
    }

    /**
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * @param \DateTime $dateStart
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
    }

    /**
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;

        if ($this->status == self::STATUS_WORKING) {
            if (!$this->getDateStart()) {
                $this->setDateStart(new \DateTime());
            }
        }
        if ($this->status == self::STATUS_RELEASED) {
            if (!$this->getDateEnd()) {
                $this->setDateEnd(new \DateTime());
            }
        }
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusName()
    {
        if (array_key_exists($this->getStatus(), self::$statusList)) {
            return self::$statusList[$this->getStatus()];
        }
    }
}
