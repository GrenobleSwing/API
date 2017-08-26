<?php

namespace GS\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Type;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Society
 *
 * @ORM\Entity
 */
class Society
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 255
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\Type("string")
     */
    private $taxInformation;

    /**
     * @ORM\Column(type="text")
     * @Assert\Type("string")
     */
    private $vatInformation;

   /**
     * @ORM\OneToOne(targetEntity="GS\ApiBundle\Entity\Address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="phone_number")
     * @Type("libphonenumber\PhoneNumber")
     * @AssertPhoneNumber(defaultRegion="FR")
     */
   private $phoneNumber;


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
     * Set name
     *
     * @param string $name
     *
     * @return Society
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
    public function getname()
    {
        return $this->name;
    }

    /**
     * Set phoneNumber
     *
     * @param phone_number $phoneNumber
     *
     * @return Society
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
     * @return Society
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
     * @return Society
     */
    public function setEmail($email)
    {
        $this->email = $email;

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
     * Set taxInformation
     *
     * @param string $taxInformation
     *
     * @return Society
     */
    public function setTaxInformation($taxInformation)
    {
        $this->taxInformation = $taxInformation;

        return $this;
    }

    /**
     * Get taxInformation
     *
     * @return string
     */
    public function getTaxInformation()
    {
        return $this->taxInformation;
    }

    /**
     * Set vatInformation
     *
     * @param string $vatInformation
     *
     * @return Society
     */
    public function setVatInformation($vatInformation)
    {
        $this->vatInformation = $vatInformation;

        return $this;
    }

    /**
     * Get vatInformation
     *
     * @return string
     */
    public function getVatInformation()
    {
        return $this->vatInformation;
    }
}
