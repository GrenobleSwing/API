<?php

namespace GS\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Activity
 *
 * @ORM\Entity
 */
class Activity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity="GS\ApiBundle\Entity\Year", inversedBy="activities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $year;

    /**
     * @ORM\OneToMany(targetEntity="GS\ApiBundle\Entity\Topic", mappedBy="activity", cascade={"persist", "remove"})
     */
    private $topics;

    /**
     * @ORM\OneToMany(targetEntity="GS\ApiBundle\Entity\Category", mappedBy="activity", cascade={"persist", "remove"})
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="GS\ApiBundle\Entity\Discount", mappedBy="activity", cascade={"persist", "remove"})
     */
    private $discounts;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->discounts = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Activity
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set year
     *
     * @param \GS\ApiBundle\Entity\Year $year
     *
     * @return Activity
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

    /**
     * Add topic
     *
     * @param \GS\ApiBundle\Entity\Topic $topic
     *
     * @return Activity
     */
    public function addTopic(\GS\ApiBundle\Entity\Topic $topic)
    {
        $this->topics[] = $topic;
        $topic->setActivity($this);

        return $this;
    }

    /**
     * Remove topic
     *
     * @param \GS\ApiBundle\Entity\Topic $topic
     */
    public function removeTopic(\GS\ApiBundle\Entity\Topic $topic)
    {
        $this->topics->removeElement($topic);
    }

    /**
     * Get topics
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTopics()
    {
        return $this->topics;
    }

    /**
     * Add category
     *
     * @param \GS\ApiBundle\Entity\Category $category
     *
     * @return Activity
     */
    public function addCategory(\GS\ApiBundle\Entity\Category $category)
    {
        $this->categories[] = $category;
        $category->setActivity($this);

        return $this;
    }

    /**
     * Remove category
     *
     * @param \GS\ApiBundle\Entity\Category $category
     */
    public function removeCategory(\GS\ApiBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add discount
     *
     * @param \GS\ApiBundle\Entity\Discount $discount
     *
     * @return Activity
     */
    public function addDiscount(\GS\ApiBundle\Entity\Discount $discount)
    {
        $this->discounts[] = $discount;
        $discount->setActivity($this);

        return $this;
    }

    /**
     * Remove discount
     *
     * @param \GS\ApiBundle\Entity\Discount $discount
     */
    public function removeDiscount(\GS\ApiBundle\Entity\Discount $discount)
    {
        $this->discounts->removeElement($discount);
    }

    /**
     * Get discounts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Activity
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Activity
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }
}
