<?php

namespace Furniture\UserBundle\Entity;

use Furniture\FactoryBundle\Entity\Factory;
use Sylius\Component\Core\Model\User as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Furniture\RetailerBundle\Entity\RetailerUserProfile;

/**
 * @UniqueEntity(
 *     fields={"usernameCanonical"},
 *     groups={"Default", "Create", "Update"},
 *     errorPath="username"
 * )
 */
class User extends BaseUser 
{
    const ROLE_CONTENT_USER   = 'ROLE_CONTENT_USER'; // @todo: remove this role?
    const ROLE_FACTORY_ADMIN  = 'ROLE_FACTORY_ADMIN';
    const ROLE_FACTORY_USER   = 'ROLE_FACTORY_USER';
    const ROLE_PUBLIC_CONTENT = 'ROLE_PUBLIC_CONTENT';

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Create"})
     */
    protected $plainPassword;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var RetailerUserProfile
     *
     * @Assert\Valid()
     */
    protected $retailerUserProfile;

    /**
     * @var bool
     */
    protected $needResetPassword = false;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
        $this->usernameCanonical = self::canonizeUsername($username);

        return $this;
    }

    /**
     * Set factory
     *
     * @param Factory $factory
     *
     * @return User
     */
    public function setFactory(Factory $factory = null)
    {
        $this->resetProfile();
        $this->factory = $factory;

        return $this;
    }

    /**
     * Get factory
     *
     * @return Factory|null
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Has factory
     *
     * @return bool
     */
    public function hasFactory()
    {
        return $this->factory ? true : false;
    }

    /**
     * Get retailer user profile
     *
     * @return RetailerUserProfile
     */
    public function getRetailerUserProfile()
    {
        return $this->retailerUserProfile;
    }
    
    /**
     * Set retailer user profile
     *
     * @param RetailerUserProfile $retailerUserProfile
     *
     * @return User
     */
    public function setRetailerUserProfile(RetailerUserProfile $retailerUserProfile)
    {
        $this->resetProfile();
        $retailerUserProfile->setUser($this);
        $this->retailerUserProfile = $retailerUserProfile;

        return $this;
    }


    /**
     * Is this content user?
     *
     * @return bool
     */
    public function isContentUser()
    {
        return $this->hasRole(self::ROLE_CONTENT_USER);
    }

    /**
     * Is this factory admin user?
     *
     * @return bool
     */
    public function isFactoryAdmin()
    {
        return $this->hasRole(self::ROLE_FACTORY_ADMIN);
    }

    /**
     * Is factory user ?
     * 
     * @return bool
     */
    public function isFactory()
    {
        return $this->factory ? true : false;
    }

    /**
     * Is retailer?
     *
     * @return bool
     */
    public function isRetailer()
    {
        if($this->retailerUserProfile 
                && $this->retailerUserProfile->getRetailerMode()
                && $this->retailerUserProfile->getRetailerProfile()
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Is no retailer?
     *
     * @return bool
     */
    public function isNoRetailer()
    {
        return !$this->isRetailer();
    }   

    /**
     * Reset profile
     */
    protected function resetProfile()
    {
        $this->retailerUserProfile = null;
    }

    /**
     * Set need reset password
     *
     * @param bool $resetPassword
     *
     * @return User
     */
    public function setNeedResetPassword($resetPassword)
    {
        $this->needResetPassword = (bool) $resetPassword;

        return $this;
    }

    /**
     * Is need reset password
     *
     * @return bool
     */
    public function isNeedResetPassword()
    {
        return $this->needResetPassword;
    }

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName()
    {
        return sprintf(
            '%s %s',
            $this->customer->getFirstName(),
            $this->customer->getLastName()
        );
    }

    /**
     * Is user disabled?
     *
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->isEnabled();
    }

    /**
     * Request for reset password
     *
     * @return string Returns the confirmation token for reset password
     */
    public function requestForResetPassword()
    {
        $this->confirmationToken = md5(uniqid(mt_rand(), true)) . md5(uniqid(mt_rand(), true));
        $this->passwordRequestedAt = new \DateTime();

        return $this->confirmationToken;
    }

    /**
     * Reset password
     *
     * @param string $newPassword
     *
     * @return User
     */
    public function resetPassword($newPassword)
    {
        $this->plainPassword = $newPassword;
        $this->confirmationToken = null;
        $this->passwordRequestedAt = null;
        $this->needResetPassword = false;

        return $this;
    }

    /**
     * Canonize username
     *
     * @param string $username
     *
     * @return string
     */
    public static function canonizeUsername($username)
    {
        return mb_strtolower($username, mb_detect_encoding($username));
    }

    /**
     * Get needResetPassword
     *
     * @return boolean
     */
    public function getNeedResetPassword()
    {
        return $this->needResetPassword;
    }

    /**
     * Remove oauthAccount
     *
     * @param \Sylius\Component\User\Model\UserOAuth $oauthAccount
     */
    public function removeOauthAccount(\Sylius\Component\User\Model\UserOAuth $oauthAccount)
    {
        $this->oauthAccounts->removeElement($oauthAccount);
    }
}
