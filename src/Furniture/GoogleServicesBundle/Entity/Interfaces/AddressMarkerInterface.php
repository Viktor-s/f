<?php
namespace Furniture\GoogleServicesBundle\Entity\Interfaces;

interface AddressMarkerInterface
{
    
    /**
     * getting string address
     * @return string
     */
    public function getAddress();
    
    /**
     * set string address
     * 
     * @param string $address
     */
    public function setAddress($address);
    
    /**
     * #return float
     */
    public function getLat();

    /**
     * 
     * @param float $latitude
     */
    public function setLat($latitude);
    
    /**
     * 
     */
    public function getLng();
    
    /**
     * 
     */
    public function setLng($longtitude);
    
    public function getCountry();
    
    public function setCountry($country);
    
    public function getLocality();
    
    public function setLocality($localy);
    
    public function getStreetNumber();
    
    public function setRoute($route);
    
    public function getRoute();
    
    public function setStreetNumber($streetNumber);

    public function getSublocalityLevel1();
    
    public function setSublocalityLevel1($levelArea);
    
    public function getSublocalityLevel2();
    
    public function setSublocalityLevel2($levelArea);
    
    public function getSublocalityLevel3();
    
    public function setSublocalityLevel3($levelArea);
    
    public function getSublocalityLevel4();
    
    public function setSublocalityLevel4($levelArea);
    
    public function getSublocalityLevel5();
    
    public function setSublocalityLevel5($levelArea);
    
    public function getAdministrativeAreaLevel1();
    
    public function setAdministrativeAreaLevel1($levelArea);
    
    public function getAdministrativeAreaLevel2();
    
    public function setAdministrativeAreaLevel2($levelArea);
    
    public function getAdministrativeAreaLevel3();
    
    public function setAdministrativeAreaLevel3($levelArea);
    
    public function getAdministrativeAreaLevel4();
    
    public function setAdministrativeAreaLevel4($levelArea);
    
    public function getAdministrativeAreaLevel5();
    
    public function setAdministrativeAreaLevel5($levelArea);
}
