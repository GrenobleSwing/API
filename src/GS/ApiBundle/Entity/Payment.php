<?php

namespace GS\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;

/**
 * Payment
 *
 * @ORM\Entity
 */
class Payment
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Valid types: CASH, TRANSFER, CHECK, PAYPAL, CARD
     * 
     * @ORM\Column(type="string", length=10)
     */
    private $type;

    /**
     * States:
     *   - DRAFT: for online payments: once a payment is received moved to PAID
     *   - PAID
     * 
     * @ORM\Column(type="string", length=6)
     */
    private $state = 'DRAFT';

    /**
     * @ORM\Column(type="float")
     */
    private $amount = 0.0;

    /**
     * @ORM\Column(type="date")
     * @Type("DateTime<'Y-m-d'>")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="GS\ApiBundle\Entity\PaymentItem", mappedBy="payment")
     * @Type("Relation<PaymentItem>")
     */
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->date = new \DateTime();
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
     * @return Payment
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
     * Set amount
     *
     * @param float $amount
     *
     * @return Payment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Add item
     *
     * @param \GS\ApiBundle\Entity\PaymentItem $item
     *
     * @return Payment
     */
    public function addItem(\GS\ApiBundle\Entity\PaymentItem $item)
    {
        $this->items[] = $item;
        $this->amount += $item->getAmount();

        return $this;
    }

    /**
     * Remove item
     *
     * @param \GS\ApiBundle\Entity\PaymentItem $item
     */
    public function removeItem(\GS\ApiBundle\Entity\PaymentItem $item)
    {
        $this->amount -= $item->getAmount();
        $this->items->removeElement($item);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Payment
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Payment
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
