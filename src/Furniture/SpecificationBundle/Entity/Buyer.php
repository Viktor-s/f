<?php

namespace Furniture\SpecificationBundle\Entity;

use Furniture\CommonBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Furniture\RetailerBundle\Entity\RetailerUserProfile;
use Furniture\GoogleServicesBundle\Entity\Interfaces\AddressMarkerInterface;

class Buyer implements AddressMarkerInterface
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
     *
     * @var string
     */
    private $country;
    
    /**
     *
     * @var string
     */
    private $locality;
    
    /**
     *
     * @var string
     */
    private $route;
    
    /**
     *
     * @var string
     */
    private $streetNumber;

    /**
     *
     * @var string
     */
    private $sublocalityLevel1;
    
    /**
     *
     * @var string
     */
    private $sublocalityLevel2;
    
    /**
     *
     * @var string
     */
    private $sublocalityLevel3;
    
    /**
     *
     * @var string
     */
    private $sublocalityLevel4;
    
    /**
     *
     * @var string
     */
    private $sublocalityLevel5;
    
    /**
     *
     * @var string
     */
    private $administrativeAreaLevel1;

    /**
     *
     * @var string
     */
    private $administrativeAreaLevel2;
    
    /**
     *
     * @var string
     */
    private $administrativeAreaLevel3;
    
    /**
     *
     * @var string
     */
    private $administrativeAreaLevel4;
    
    /**
     *
     * @var string
     */
    private $administrativeAreaLevel5;
    
    /**
     *
     * @var float
     */
    private $lat;
    
    /**
     *
     * @var float
     */
    private $lng;
    
    /**
     * @var integer
     *
     * @internal This is a internal field. For use this field, please use methods: setCountSpecifications and
     *           getCountSpecifications
     */
    private $countSpecifications;

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
     * @return \Furniture\SpecificationBundle\Entity\Buyer
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
     * @return \Furniture\SpecificationBundle\Entity\Buyer
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
     * @return \Furniture\SpecificationBundle\Entity\Buyer
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
     * 
     * @param type $sale
     * @return \Furniture\SpecificationBundle\Entity\Buyer
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
     * Set count specifications
     *
     * @param int $countSpecifications
     *
     * @return Buyer
     */
    public function setCountSpecifications($countSpecifications)
    {
        $this->countSpecifications = $countSpecifications;

        return $this;
    }

    /**
     * Get count specifications
     *
     * @return integer
     */
    public function getCountSpecifications()
    {
        return $this->countSpecifications;
    }

    /**
     * 
     * @return string
     */
    public function getAdministrativeAreaLevel1() {
        return $this->administrativeAreaLevel1;
    }

    /**
     * 
     * @return string
     */
    public function getAdministrativeAreaLevel2() {
        return $this->administrativeAreaLevel2;
    }

    /**
     * 
     * @return string
     */
    public function getAdministrativeAreaLevel3() {
        return $this->administrativeAreaLevel3;
    }

    /**
     * 
     * @return string
     */
    public function getAdministrativeAreaLevel4() {
        return $this->administrativeAreaLevel4;
    }

    /**
     * 
     * @return string
     */
    public function getAdministrativeAreaLevel5() {
        return $this->administrativeAreaLevel5;
    }

    /**
     * 
     * @return string
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * 
     * @return string
     */
    public function getLocality() {
        return $this->localy;
    }

    /**
     * 
     * @param string $levelArea
     * @return \Furniture\SpecificationBundle\Entity\Buyer
     */
    public function setAdministrativeAreaLevel1($levelArea) {
        $this->administrativeAreaLevel1 = $levelArea;
        return $this;
    }

    /**
     * 
     * @param string $levelArea
     * @return \Furniture\SpecificationBundle\Entity\Buyer
     */
    public function setAdministrativeAreaLevel2($levelArea) {
        $this->administrativeAreaLevel2 = $levelArea;
        return $this;
    }

    /**
     * 
     * @param string $levelArea
     * @return \Furniture\SpecificationBundle\Entity\Buyer
     */
    public function setAdministrativeAreaLevel3($levelArea) {
        $this->administrativeAreaLevel3 = $levelArea;
        return $this;
    }

    /**
     * 
     * @param string $levelArea
     * @return \Furniture\SpecificationBundle\Entity\Buyer
     */
    public function setAdministrativeAreaLevel4($levelArea) {
        $this->administrativeAreaLevel4 = $levelArea; 
        return $this;
    }

    /**
     * 
     * @param string $levelArea
     * @return \Furniture\SpecificationBundle\Entity\Buyer
     */
    public function setAdministrativeAreaLevel5($levelArea) {
        $this->administrativeAreaLevel5 = $levelArea;
        return $this;
    }

    /**
     * 
     * @param string $country
     * @return \Furniture\SpecificationBundle\Entity\Buyer
     */
    public function setCountry($country) {
        $this->country = $country;
        return $this;
    }

    /**
     * 
     * @param string $localy
     * @return \Furniture\SpecificationBundle\Entity\Buyer
     */
    public function setLocality($localy) {
        $this->locality = $localy;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreetNumber() {
       return $this->streetNumber; 
    }

    /**
     * 
     * @param street $streetNumber
     * @return \Furniture\SpecificationBundle\Entity\Buyer
     */
    public function setStreetNumber($streetNumber) {
        $this->streetNumber = $streetNumber;
        return $this;
    }

    /**
     * 
     * @return sting
     */
    public function getSublocalityLevel1() {
        return $this->sublocalityLevel1;
    }

    /**
     * 
     * @return sting
     */
    public function getSublocalityLevel2() {
        return $this->sublocalityLevel2;
    }

    /**
     * 
     * @return sting
     */
    public function getSublocalityLevel3() {
        return $this->sublocalityLevel3;
    }

    /**
     * 
     * @return sting
     */
    public function getSublocalityLevel4() {
        return $this->sublocalityLevel4;
    }

    /**
     * 
     * @return sting
     */
    public function getSublocalityLevel5() {
        return $this->sublocalityLevel5;
    }

    /**
     * 
     * @param string $levelArea
     * @return \Furniture\SpecificationBundle\Entity\Buyer
     */
    public function setSublocalityLevel1($levelArea) {
        $this->sublocalityLevel1 = $levelArea;
        return $this;
    }

    /**
     * 
     * @param string $levelArea
     * @return \Furniture\SpecificationBundle\Entity\Buyer
     */
    public function setSublocalityLevel2($levelArea) {
        $this->sublocalityLevel2 = $levelArea;
        return $this;
    }

    /**
     * 
     * @param string $levelArea
     * @return \Furniture\SpecificationBundle\Entity\Buyer
     */
    public function setSublocalityLevel3($levelArea) {
        $this->sublocalityLevel3 = $levelArea;
        return $this;
    }

    /**
     * 
     * @param string $levelArea
     * @return \Furniture\SpecificationBundle\Entity\Buyer
     */
    public function setSublocalityLevel4($levelArea) {
        $this->sublocalityLevel4 = $levelArea;
        return $this;
    }

    /**
     * 
     * @param string $levelArea
     * @return \Furniture\SpecificationBundle\Entity\Buyer
     */
    public function setSublocalityLevel5($levelArea) {
        $this->sublocalityLevel5 = $levelArea;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * 
     * @param string $route
     * @return \Furniture\SpecificationBundle\Entity\Buyer
     */
    public function setRoute($route) {
        $this->route = $route;
        return $this;
    }
    
    /**
     * 
     * @return float
     */
    public function getLat() {
        return $this->lat;
    }
    
    /**
     * 
     * @return float
     */
    public function getLng() {
        return $this->lng;
    }

    /**
     * 
     * @param float $latitude
     * @return \Furniture\SpecificationBundle\Entity\Buyer
     */
    public function setLat($latitude) {
        $this->lat = $latitude;
        return $this;
    }
    
    /**
     * 
     * @param float $longtitude
     * @return \Furniture\SpecificationBundle\Entity\Buyer
     */
    public function setLng($longtitude) {
        $this->lng = $longtitude;
        return $this;
    }
    
    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName()
    {
        return (string) $this;
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
