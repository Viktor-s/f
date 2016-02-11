<?php

namespace Furniture\GoogleServicesBundle\Entity\Interfaces;

interface AddressMarkerInterface
{
    /**
     * Getting string address
     *
     * @return string
     */
    public function getAddress();

    /**
     * Get latitude of position
     *
     * @return float
     */
    public function getLat();

    /**
     * Set latitude of position
     *
     * @param float $latitude
     */
    public function setLat($latitude);
    
    /**
     * Get longitude of position
     *
     * @return float
     */
    public function getLng();
    
    /**
     * Set longitude of position
     *
     * @param float $longitude
     */
    public function setLng($longitude);

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry();

    /**
     * Set country
     *
     * @param string $country
     */
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
