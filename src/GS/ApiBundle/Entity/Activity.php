<?php

namespace GS\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use JMS\Serializer\Annotation\Type;

/**
 * Activity
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *         "get_activity",
 *         parameters = { "activity" = "expr(object.getId())" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *         excludeIf = "expr(not is_granted('view', object))"
 *     )
 * )
 * @Hateoas\Relation(
 *     "edit",
 *     href = @Hateoas\Route(
 *         "edit_activity",
 *         parameters = { "activity" = "expr(object.getId())" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *         excludeIf = "expr(not is_granted('edit', object))"
 *     )
 * )
 * @Hateoas\Relation(
 *     "remove",
 *     href = @Hateoas\Route(
 *         "remove_activity",
 *         parameters = { "activity" = "expr(object.getId())" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *         excludeIf = "expr(not is_granted('delete', object))"
 *     )
 * )
 * @Hateoas\Relation(
 *     "new_topic",
 *     href = @Hateoas\Route(
 *         "new_activity_topic",
 *         parameters = { "activity" = "expr(object.getId())" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *         excludeIf = "expr(not is_granted('edit', object))"
 *     )
 * )
 * @Hateoas\Relation(
 *     "new_category",
 *     href = @Hateoas\Route(
 *         "new_activity_category",
 *         parameters = { "activity" = "expr(object.getId())" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *         excludeIf = "expr(not is_granted('edit', object))"
 *     )
 * )
 * @Hateoas\Relation(
 *     "new_discount",
 *     href = @Hateoas\Route(
 *         "new_activity_discount",
 *         parameters = { "activity" = "expr(object.getId())" }
 *     ),
 *     exclusion = @Hateoas\Exclusion(
 *         excludeIf = "expr(not is_granted('edit', object))"
 *     )
 * )
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
     * States: draft, open, close
     *
     * @ORM\Column(type="string", length=16)
     */
    private $state = 'DRAFT';

    /**
     * @ORM\Column(type="boolean")
     */
    private $membership = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $membersOnly = false;

    /**
     * @ORM\ManyToOne(targetEntity="GS\ApiBundle\Entity\Topic")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Type("Relation")
     */
    private $membershipTopic = null;

    /**
     * @ORM\ManyToOne(targetEntity="GS\ApiBundle\Entity\Year", inversedBy="activities")
     * @ORM\JoinColumn(nullable=false)
     * @Type("Relation")
     */
    private $year;

    /**
     * @ORM\OneToMany(targetEntity="GS\ApiBundle\Entity\Topic", mappedBy="activity", cascade={"persist", "remove"})
     * @Type("Relation<Topic>")
     */
    private $topics;

    /**
     * @ORM\OneToMany(targetEntity="GS\ApiBundle\Entity\Category", mappedBy="activity", cascade={"persist", "remove"})
     * @Type("Relation<Category>")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="GS\ApiBundle\Entity\Discount", mappedBy="activity", cascade={"persist", "remove"})
     * @Type("Relation<Discount>")
     */
    private $discounts;

    /**
     * @ORM\ManyToMany(targetEntity="GS\ApiBundle\Entity\User")
     * @Type("Relation<User>")
     */
    private $owners;


    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->getMembership() == false) {
            return;
        }
        foreach ($this->getYear()->getActivities() as $activity) {
            if ($activity === $this) {
                continue;
            } elseif (true == $activity->isMembership()) {
                $context->buildViolation('Only one membership per year.')
                        ->addViolation();
                break;
            }
        }
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->discounts = new ArrayCollection();
        $this->owners = new ArrayCollection();
    }

    /**
     * Add owner
     *
     * @param \GS\ApiBundle\Entity\User $owner
     *
     * @return Activity
     */
    public function addOwner(\GS\ApiBundle\Entity\User $owner)
    {
        $this->owners[] = $owner;
        return $this;
    }

    /**
     * Remove owner
     *
     * @param \GS\ApiBundle\Entity\User $owner
     */
    public function removeOwner(\GS\ApiBundle\Entity\User $owner)
    {
        $this->owners->removeElement($owner);
        $owner->removeActivity($this);
    }

    /**
     * Get owners
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOwners()
    {
        return $this->owners;
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

    /**
     * Set membership
     *
     * @param string $membership
     *
     * @return Activity
     */
    public function setMembership($membership)
    {
        $this->membership = $membership;

        return $this;
    }

    /**
     * Is membership
     *
     * @return boolean
     */
    public function isMembership()
    {
        return $this->membership;
    }

    /**
     * Set membersOnly
     *
     * @param boolean $membersOnly
     *
     * @return Activity
     */
    public function setMembersOnly($membersOnly)
    {
        $this->membersOnly = $membersOnly;

        return $this;
    }

    /**
     * Get membersOnly
     *
     * @return boolean
     */
    public function getMembersOnly()
    {
        return $this->membersOnly;
    }

    /**
     * Get membership
     *
     * @return boolean
     */
    public function getMembership()
    {
        return $this->membership;
    }

    /**
     * Set membershipTopic
     *
     * @param \GS\ApiBundle\Entity\Topic $membershipTopic
     *
     * @return Activity
     */
    public function setMembershipTopic(\GS\ApiBundle\Entity\Topic $membershipTopic = null)
    {
        $this->membershipTopic = $membershipTopic;

        return $this;
    }

    /**
     * Get membershipTopic
     *
     * @return \GS\ApiBundle\Entity\Topic
     */
    public function getMembershipTopic()
    {
        return $this->membershipTopic;
    }
}
