<?php

namespace GS\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Account
 *
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *         "get_account",
 *         parameters = { "account" = "expr(object.getId())" }
 *     )
 * )
 * @Hateoas\Relation(
 *     "edit",
 *     href = @Hateoas\Route(
 *         "edit_account",
 *         parameters = { "account" = "expr(object.getId())" }
 *     )
 * )
 * @Hateoas\Relation(
 *     "remove",
 *     href = @Hateoas\Route(
 *         "remove_account",
 *         parameters = { "account" = "expr(object.getId())" }
 *     )
 * )
 * @ORM\Entity
 */
class Account
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $lastName;

    /**
     * @ORM\Column(type="date")
     */
    private $birthDate;

    /**
     * @ORM\Column(type="phone_number")
     * @Type("libphonenumber\PhoneNumber")
     * @AssertPhoneNumber(defaultRegion="FR")
     */
   private $phoneNumber;

   /**
     * @ORM\OneToOne(targetEntity="GS\ApiBundle\Entity\Address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $address;

    /**
     * @ORM\Column(type="boolean")
     */
    private $student = false;

   /**
    * @ORM\OneToOne(targetEntity="GS\ApiBundle\Entity\User", cascade={"persist", "remove"})
    * @SerializedName("userId")
    * @Type("Relation")
    */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="GS\ApiBundle\Entity\Payment", mappedBy="account", cascade={"persist", "remove"})
     * @Type("Relation<Payment>")
     */
    private $payments;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->payments = new ArrayCollection();
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Account
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Account
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     *
     * @return Account
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set phoneNumber
     *
     * @param phone_number $phoneNumber
     *
     * @return Account
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return phone_number
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set address
     *
     * @param \GS\ApiBundle\Entity\Address $address
     *
     * @return Account
     */
    public function setAddress(\GS\ApiBundle\Entity\Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \GS\ApiBundle\Entity\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Account
     */
    public function setEmail($email)
    {
        $this->email = $email;
        
        // If the email is modified, the user login should be modified.
        if (null !== $this->user) {
            $this->user->setEmail($email);
        }

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set user
     *
     * @param \GS\ApiBundle\Entity\User $user
     *
     * @return Account
     */
    public function setUser(\GS\ApiBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \GS\ApiBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set student
     *
     * @param boolean $student
     *
     * @return Account
     */
    public function setStudent($student)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * Is student
     *
     * @return boolean
     */
    public function isStudent()
    {
        return $this->student;
    }
    
    /**
     * Is member
     *
     * @return boolean
     */
    public function isMember()
    {
        return true;
    }

    /**
     * Get displayName
     *
     * @return float
     */
    public function getDisplayName()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * Add payment
     *
     * @param \GS\ApiBundle\Entity\Payment $payment
     *
     * @return Account
     */
    public function addPayment(\GS\ApiBundle\Entity\Payment $payment)
    {
        $this->payments[] = $payment;
        $payment->setAccount($this);

        return $this;
    }

    /**
     * Remove payment
     *
     * @param \GS\ApiBundle\Entity\Payment $payment
     */
    public function removePayment(\GS\ApiBundle\Entity\Payment $payment)
    {
        $this->payments->removeElement($payment);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPayments()
    {
        return $this->payments;
    }
}
