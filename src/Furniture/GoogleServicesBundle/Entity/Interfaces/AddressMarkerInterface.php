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
    
}
