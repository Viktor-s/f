<?php

namespace Furniture\UserBundle\Entity;

use Sylius\Component\Core\Model\Customer as BaseCustomer;
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity(
 *     fields={"emailCanonical"},
 *     groups={"Default", "Create", "Update"},
 *     errorPath="email"
 * )
 */
class Customer extends BaseCustomer
{
    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Create", "Update"})
     * @Assert\Email(strict=true, groups={"Create", "Update"})
     */
    protected $email;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Create", "Update"})
     */
    protected $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Create", "Update"})
     */
    protected $gender = CustomerInterface::UNKNOWN_GENDER;

    /**
     * @var User
     *
     * @Assert\Valid()
     */
    protected $user;

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;
        $this->emailCanonical = self::canonizeEmail($email);

        return $this;
    }

    /**
     * Canonize email
     *
     * @param string $email
     *
     * @return string
     */
    public static function canonizeEmail($email)
    {
        return mb_strtolower($email, mb_detect_encoding($email));
    }
}
