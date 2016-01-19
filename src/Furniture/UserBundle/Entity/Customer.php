<?php

namespace Furniture\UserBundle\Entity;

use Sylius\Component\Core\Model\Customer as BaseCustomer;
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
}
