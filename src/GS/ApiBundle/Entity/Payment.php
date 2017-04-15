<?php

namespace GS\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;
use PayPal\Api\ItemList;

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
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $paypalPaymentId = null;

    /**
     * @ORM\OneToMany(targetEntity="GS\ApiBundle\Entity\PaymentItem", mappedBy="payment", cascade={"persist", "remove"})
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

    private function updateRegistrations()
    {
        if ('PAID' == $this->getState()) {
            foreach ($this->getItems() as $item) {
                $registration = $item->getRegistration();
                $registration->pay($item->getAmount());
            }
        }
    }

    public function updateAmount()
    {
        $amount = 0.0;
        foreach ($this->getItems() as $item) {
            $amount += $item->getAmount();
        }
        $this->setAmount($amount);
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
        $item->setPayment($this);
        $this->updateAmount();
        // Mark all the resitrations (one per item) as paid
        $this->updateRegistrations();

        return $this;
    }

    /**
     * Remove item
     *
     * @param \GS\ApiBundle\Entity\PaymentItem $item
     */
    public function removeItem(\GS\ApiBundle\Entity\PaymentItem $item)
    {
        $this->items->removeElement($item);
        // The Registration removed is not paid anymore but only validated
        $item->getRegistration()->setAmountPaid(0.0)->validate();
        $this->updateAmount();
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
        
        // Mark all the resitrations (one per item) as paid if state is changed
        // to PAID otherwise do nothing.
        $this->updateRegistrations();

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

    /**
     * Set paypalPaymentId
     *
     * @param string $paypalPaymentId
     *
     * @return Payment
     */
    public function setPaypalPaymentId($paypalPaymentId)
    {
        $this->paypalPaymentId = $paypalPaymentId;

        return $this;
    }

    /**
     * Get paypalPaymentId
     *
     * @return string
     */
    public function getPaypalPaymentId()
    {
        return $this->paypalPaymentId;
    }

    /**
     * Get PayPal Payment Item List
     *
     * @return \PayPal\Api\ItemList
     */
    public function getPaypalPaymentItemList()
    {
        $itemList = new ItemList();
        foreach ($this->getItems() as $item) {
            // One item for the registration and one for the discount if any
            foreach ($item->getPaypalPaymentItems() as $paypalItem) {
                $itemList->addItem($paypalItem);
            }
        }
        return $itemList;
    }

}
