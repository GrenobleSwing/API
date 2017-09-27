<?php

namespace GS\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Certificate
 *
 * @ORM\Entity(repositoryClass="GS\ApiBundle\Repository\CertificateRepository")
 */
class Certificate
{
    const STUDENT = 'student';
    const UNEMPLOYED = 'unemployed';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=12)
     * @Assert\Choice({"student", "unemployed"})
     */
    private $type = "student";

    /**
     * @ORM\Column(type="date")
     * @Assert\Date()
     */
    private $startDate;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date()
     */
    private $endDate;

    /**
     * @ORM\ManyToOne(targetEntity="GS\ApiBundle\Entity\Account")
     * @ORM\JoinColumn(nullable=false)
     * @Type("Relation")
     */
    private $account;

    /**
     * @ORM\ManyToOne(targetEntity="GS\ApiBundle\Entity\Year")
     * @ORM\JoinColumn(nullable=false)
     * @Type("Relation")
     */
    private $year;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->startDate = new \DateTime();
        $this->endDate = new \DateTime();
        $this->endDate->add(new \DateInterval('P2M'));
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Certificate
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Certificate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Certificate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set account
     *
     * @param \GS\ApiBundle\Entity\Account $account
     *
     * @return Certificate
     */
    public function setAccount(\GS\ApiBundle\Entity\Account $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \GS\ApiBundle\Entity\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set year
     *
     * @param \GS\ApiBundle\Entity\Year $year
     *
     * @return Certificate
     */
    public function setYear(\GS\ApiBundle\Entity\Year $year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return \GS\ApiBundle\Entity\Year
     */
    public function getYear()
    {
        return $this->year;
    }

    public function getDisplay()
    {
        return $this->getAccount()->getDisplayName() . ' - ' . $this->getType();
    }
}
