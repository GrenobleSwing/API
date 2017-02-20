<?php

namespace GS\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Subscription
 *
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"topic", "account"},
 *     message="This subscription already exists."
 * )
 */
class Subscription
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $state;

    /**
     * @ORM\Column(type="array")
     */
    private $options;

    /**
     * @ORM\ManyToOne(targetEntity="GS\ApiBundle\Entity\Topic")
     * @ORM\JoinColumn(nullable=false)
     * @SerializedName("topicId")
     * @Type("Relation")
     */
    private $topic;

    /**
     * @ORM\ManyToOne(targetEntity="GS\ApiBundle\Entity\Account")
     * @ORM\JoinColumn(nullable=false)
     * @SerializedName("accountId")
     * @Type("Relation")
     */
    private $account;

    public function __construct()
    {
        $this->options = array();
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
     * Set role
     *
     * @param string $role
     *
     * @return Subscription
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Subscription
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

    public function addOption($option)
    {
        if (!in_array($option, $this->options, true)) {
            $this->options[] = $option;
        }
        return $this;
    }
    
    public function removeOption($option)
    {
        if (($key = array_search($option, $this->options)) != false) {
            unset($this->options[$key]);
        }
    }
    
    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }


    /**
     * Set options
     *
     * @param array $options
     *
     * @return Subscription
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Set topic
     *
     * @param \GS\ApiBundle\Entity\Topic $topic
     *
     * @return Subscription
     */
    public function setTopic(\GS\ApiBundle\Entity\Topic $topic)
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Get topic
     *
     * @return \GS\ApiBundle\Entity\Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Set account
     *
     * @param \GS\ApiBundle\Entity\Account $account
     *
     * @return Subscription
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
}
