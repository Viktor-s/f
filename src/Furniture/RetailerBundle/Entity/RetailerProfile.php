<?php
namespace Furniture\RetailerBundle\Entity;

use Furniture\CommonBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\RetailerBundle\Entity\RetailerProfileLogoImage;

class RetailerProfile
{
    /**
     *
     * @var int
     */
    private $id;
    
    private $users;
    
    private $name;
    
    private $address;
    
    private $phones = [];
    
    private $emails = [];
    
    /**
     *
     * @var \Furniture\RetailerBundle\Entity\RetailerProfileLogoImage
     */
    private $logoImage;
    
    function __construct() {
        $this->users = new ArrayCollection();
    }
    
    /**
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
    
    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $users
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setUsers(Collection $users)
    {
        $this->users = $users;
        return $this;
    }
    
    /**
     * 
     * @return bool
     */
    public function hasUsers()
    {
        return (bool)!$this->users->isEmpty();
    }
    
    /**
     * 
     * @param \Furniture\CommonBundle\Entity\User $user
     * @return bool
     */
    public function hasUser(User $user)
    {
        return $this->users->contains($user);
    }


    /**
     * 
     * @param  \Furniture\CommonBundle\Entity\User $user
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setUser(User $user)
    {
        if(!$this->hasUser($user)){
            $user->setRetailerProfile($this);
            $this->users->add($user);
        }
        return $this;
    }
    
    /**
     * 
     * @param \Furniture\CommonBundle\Entity\User $user
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function removeUser(User $user)
    {
        if($this->hasUser($user)){
            $this->users->removeElement($user);
        }
        return $this;
    }
    
    /**
     * 
     * @param str $name
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * 
     * @return str
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * 
     * @param str $address
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }
    
    /**
     * 
     * @return str
     */
    public function getAddress()
    {
        return $this->address;
    }
    
    /**
     * 
     * @param str $phone
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setPhones(array $phone)
    {
        $this->phones = $phone;
        return $this;
    }
    
    /**
     * 
     * @return str
     */
    public function getPhones()
    {
        return $this->phones;
    }
    
    /**
     * 
     * @param array $emails
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setEmails(array $emails)
    {
        $this->emails = $emails;
        return $this;
    }
    
    /**
     * 
     * @return array
     */
    public function getEmails()
    {
        return $this->emails;
    }
    
    /**
     * 
     * @return \Furniture\RetailerBundle\Entity\RetailerProfileLogoImage
     */
    public function getLogoImage()
    {
        return $this->logoImage;
    }
    
    /**
     * 
     * @param \Furniture\RetailerBundle\Entity\RetailerProfileLogoImage $logoImage
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function setLogoImage(RetailerProfileLogoImage $logoImage)
    {
        $logoImage->setRetailerProfile($this);
        $this->logoImage = $logoImage;
        return $this;
    }
    
}

