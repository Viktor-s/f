<?php

namespace Furniture\SpecificationBundle\Entity;

use Furniture\CommonBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class Buyer
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var User
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
     * @param User $user
     *
     * @return Buyer
     */
    public function setCreator(User $user)
    {
        $this->creator = $user;

        return $this;
    }

    /**
     * Get creator
     *
     * @return User
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
