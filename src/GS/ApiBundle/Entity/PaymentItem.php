<?php

namespace GS\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use PayPal\Api\Item;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\Type("float")
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


    private function getDiscountAmount($amount)
    {
        $discount = $this->getDiscount();
        if(null !== $discount) {
            if ($discount->getType() == 'percent') {
                return $amount * $discount->getValue() / 100;
            } else {
                return $discount->getValue();
            }
        }
        return 0;
    }

    private function updateAmount()
    {
        if (null === $this->getRegistration()) {
            return;
        }
        $amount = $this->getRegistration()->getTopic()
                ->getCategory()->getPrice();

        // Apply the discount if needed
        $amount -= $this->getDiscountAmount($amount);

        // Substract the amount already paid
        $amount -= $this->getRegistration()->getAmountPaid();

        // Save the amount
        $this->setAmount($amount);

        if (null !== $this->getPayment()) {
            $this->getPayment()->updateAmount();
        }
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
        $this->updateAmount();

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
    public function setDiscount(\GS\ApiBundle\Entity\Discount $discount = null)
    {
        $this->discount = $discount;
        $this->updateAmount();

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

    /**
     * Get PayPal Payment Item
     *
     * @return \PayPal\Api\Item[]
     */
    public function getPaypalPaymentItems()
    {
        $amount = $this->getRegistration()->getTopic()
                ->getCategory()->getPrice();

        $item = new Item();
        $item->setName($this->getRegistration()->getTopic()->getTitle())
                ->setDescription($this->getRegistration()->getTopic()->getDescription())
                ->setCurrency('EUR')
                ->setQuantity(1)
                ->setTax(0)
                ->setPrice($amount);

        $itemList = array($item);

        $alreadyPaid = $this->getRegistration()->getAmountPaid();
        if (0 < $alreadyPaid) {
            $item2 = new Item();
            $item2->setName('Deja paye')
                    ->setCurrency('EUR')
                    ->setQuantity(1)
                    ->setTax(0)
                    ->setPrice(-1 * $alreadyPaid);
            $itemList[] = $item2;
        }

        $discountAmount = $this->getDiscountAmount($amount);
        if (0 < $discountAmount) {
            $item3 = new Item();
            $item3->setName('Reduction')
                    ->setDescription($this->getDiscount()->getName())
                    ->setCurrency('EUR')
                    ->setQuantity(1)
                    ->setTax(0)
                    ->setPrice(-1 * $discountAmount);
            $itemList[] = $item3;
        }

        return $itemList;
    }
}
