<?php

namespace Furniture\SpecificationBundle\Entity;

use Furniture\CommonBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Furniture\RetailerBundle\Entity\RetailerUserProfile;

class Buyer
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var RetailerUserProfile
     */
    private $creator;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $secondName;

    /**
     * In percentage
     *
     * @var float
     *
     * @Assert\Range(min = 0, max = 100)
     */
    private $sale = 0;

    /**
     * @var string
     *
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @Assert\Length(max=255)
     */
    private $address;

    /**
     * @var string
     *
     * @Assert\Length(max=255)
     */
    private $phone;

    /**
     * @var BuyerImage
     */
    private $image;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set creator
     *
     * @param \Furniture\RetailerBundle\Entity\RetailerUserProfile $creator
     *
     * @return Buyer
     */
    public function setCreator(RetailerUserProfile $creator)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \Furniture\RetailerBundle\Entity\RetailerUserProfile
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Get created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set first name
     *
     * @param string $firstName
     *
     * @return Buyer
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set second name
     *
     * @param string $secondName
     *
     * @return Buyer
     */
    public function setSecondName($secondName)
    {
        $this->secondName = $secondName;

        return $this;
    }

    /**
     * Get second name
     *
     * @return string
     */
    public function getSecondName()
    {
        return $this->secondName;
    }

    /**
     * Set sale
     *
     * @param float $sale
     *
     * @return Buyer
     */
    public function setSale($sale)
    {
        $this->sale = $sale;

        return $this;
    }

    /**
     * Get sale
     *
     * @return float
     */
    public function getSale()
    {
        return $this->sale;
    }

    /**
     * Has sale?
     *
     * @return bool
     */
    public function hasSale()
    {
        return $this->sale > 0;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Buyer
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
     * Set address
     *
     * @param string $address
     *
     * @return Buyer
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set phone number
     *
     * @param string $phone
     *
     * @return Buyer
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set image
     *
     * @param BuyerImage $image
     *
     * @return Buyer
     */
    public function setImage(BuyerImage $image = null)
    {
        if ($image) {
            $image->setBuyer($this);
        }

        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return Buyer
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Remove image
     *
     * @return BuyerImage
     */
    public function removeImage()
    {
        $image = $this->image;
        $this->image = null;

        return $image;
    }

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '%s %s',
            $this->firstName,
            $this->secondName
        );
    }
}
