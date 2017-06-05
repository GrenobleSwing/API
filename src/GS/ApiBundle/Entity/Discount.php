<?php

namespace GS\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

/**
 * Discount
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *         "get_discount",
 *         parameters = { "discount" = "expr(object.getId())" }
 *     )
 * )
 * @Hateoas\Relation(
 *     "edit",
 *     href = @Hateoas\Route(
 *         "edit_discount",
 *         parameters = { "discount" = "expr(object.getId())" }
 *     )
 * )
 * @Hateoas\Relation(
 *     "remove",
 *     href = @Hateoas\Route(
 *         "remove_discount",
 *         parameters = { "discount" = "expr(object.getId())" }
 *     )
 * )
 * @ORM\Entity
 */
class Discount
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
    private $name;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     */
    private $value;

    /**
     * @ORM\Column(name="`condition`", type="string", length=200)
     */
    private $condition;

    /**
     * @ORM\ManyToOne(targetEntity="GS\ApiBundle\Entity\Activity", inversedBy="discounts")
     * @ORM\JoinColumn(nullable=false)
     * @SerializedName("activityId")
     * @Type("Relation")
     */
    private $activity;


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
     * Set name
     *
     * @param string $name
     *
     * @return Discount
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Discount
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
     * Set value
     *
     * @param string $value
     *
     * @return Discount
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set condition
     *
     * @param string $condition
     *
     * @return Discount
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * Get condition
     *
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Set activity
     *
     * @param \GS\ApiBundle\Entity\Activity $activity
     *
     * @return Discount
     */
    public function setActivity(\GS\ApiBundle\Entity\Activity $activity)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * Get activity
     *
     * @return \GS\ApiBundle\Entity\Activity
     */
    public function getActivity()
    {
        return $this->activity;
    }
}
