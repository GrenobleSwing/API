<?php

namespace GS\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

/**
 * Payment
 *
 * @ORM\Entity
 */
class PaymentItem
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $amount = 0.0;

    /**
     * @ORM\ManyToOne(targetEntity="GS\ApiBundle\Entity\Registration")
     * @ORM\JoinColumn(nullable=false)
     * @SerializedName("registrationId")
     * @Type("Relation")
     */
    private $registration;

    /**
     * @ORM\ManyToOne(targetEntity="GS\ApiBundle\Entity\Discount")
     * @SerializedName("discountId")
     * @Type("Relation")
     */
    private $discount;

    /**
     * @ORM\ManyToOne(targetEntity="GS\ApiBundle\Entity\Payment", inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     * @SerializedName("paymentId")
     * @Type("Relation")
     */
    private $payment;


    public function __construct()
    {
        $this->discount = null;
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
     * Set registration
     *
     * @param \GS\ApiBundle\Entity\Registration $registration
     *
     * @return PaymentItem
     */
    public function setRegistration(\GS\ApiBundle\Entity\Registration $registration)
    {
        $this->registration = $registration;

        return $this;
    }

    /**
     * Get registration
     *
     * @return \GS\ApiBundle\Entity\Registration
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * Set discount
     *
     * @param \GS\ApiBundle\Entity\Discount $discount
     *
     * @return PaymentItem
     */
    public function setDiscount(\GS\ApiBundle\Entity\Discount $discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return \GS\ApiBundle\Entity\Discount
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set payment
     *
     * @param \GS\ApiBundle\Entity\Payment $payment
     *
     * @return PaymentItem
     */
    public function setPayment(\GS\ApiBundle\Entity\Payment $payment)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \GS\ApiBundle\Entity\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return PaymentItem
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
}
