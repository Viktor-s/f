<?php
namespace Furniture\RetailerBundle\Entity;

use Furniture\CommonBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class RetailerProfile
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Collection|User[]
     */
    private $users;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $address;

    /**
     * @var array
     */
    private $phones = [];

    /**
     * @var array
     */
    private $emails = [];
    
    /**
     * @var RetailerProfileLogoImage
     */
    private $logoImage;

    /**
     *
     * @var str
     */
    private $website;
    
    /**
     *
     * @var str
     */
    private $subtitle;
        
    /**
     *
     * @var str
     */
    private $description;


    /**
     * Construct
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * Get users
     *
     * @return Collection|User[]
     */
    public function getUsers()
    {
        return $this->users;
    }
    
    /**
     * Set users
     *
     * @param Collection|User[] $users
     *
     * @return RetailerProfile
     */
    public function setUsers(Collection $users)
    {
        $this->users = $users;

        return $this;
    }
    
    /**
     * Has users?
     *
     * @return bool
     */
    public function hasUsers()
    {
        return (bool)!$this->users->isEmpty();
    }
    
    /**
     * Has user?
     *
     * @param User $user
     *
     * @return bool
     */
    public function hasUser(User $user)
    {
        return $this->users->contains($user);
    }


    /**
     * Add user
     *
     * @param  User $user
     *
     * @return RetailerProfile
     */
    public function addUser(User $user)
    {
        if(!$this->hasUser($user)){
            $user->setRetailerProfile($this);
            $this->users->add($user);
        }

        return $this;
    }
    
    /**
     * Remove user
     *
     * @param User $user
     *
     * @return RetailerProfile
     */
    public function removeUser(User $user)
    {
        if($this->hasUser($user)){
            $this->users->removeElement($user);
        }

        return $this;
    }
    
    /**
     * Set name
     *
     * @param string $name
     *
     * @return RetailerProfile
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
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set address
     *
     * @param string $address
     *
     * @return RetailerProfile
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
     * Set phones
     *
     * @param array $phones
     *
     * @return RetailerProfile
     */
    public function setPhones(array $phones)
    {
        $this->phones = $phones;

        return $this;
    }
    
    /**
     * Get phones
     *
     * @return string
     */
    public function getPhones()
    {
        return $this->phones;
    }
    
    /**
     * Set emails
     *
     * @param array $emails
     *
     * @return RetailerProfile
     */
    public function setEmails(array $emails)
    {
        $this->emails = $emails;

        return $this;
    }
    
    /**
     * Get emails
     *
     * @return array
     */
    public function getEmails()
    {
        return $this->emails;
    }
    
    /**
     * Get logo image
     *
     * @return RetailerProfileLogoImage
     */
    public function getLogoImage()
    {
        return $this->logoImage;
    }

    /**
     * Remove logo image
     *
     * @return RetailerProfileLogoImage
     */
    public function removeLogoImage()
    {
        $logoImage = $this->logoImage;
        $this->logoImage = null;

        return $logoImage;
    }
    
    /**
     * Set logo image
     *
     * @param RetailerProfileLogoImage $logoImage
     *
     * @return RetailerProfile
     */
    public function setLogoImage(RetailerProfileLogoImage $logoImage)
    {
        $logoImage->setRetailerProfile($this);
        $this->logoImage = $logoImage;

        return $this;
    }

    /**
     * 
     * @return str
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * 
     * @param str $website
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }

    /**
     * 
     * @return str
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * 
     * @param str $subtitle
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    /**
     * 
     * @return str
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * 
     * @param str $description
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
    }
}
