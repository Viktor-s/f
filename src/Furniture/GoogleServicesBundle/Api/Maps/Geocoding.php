<?php
namespace Furniture\GoogleServicesBundle\Api\Maps;

class Geocoding
{
    
    /**
     *
     * @var google maps api key
     */
    private $apiKey;
    
    private $googleServiceUrl = 'https://maps.googleapis.com/maps/api/geocode/json';
            
    private $requestStack;
            
    function __construct($apiKey, $requestStack) {
        $this->apiKey = $apiKey;
        $this->requestStack = $requestStack;
    }

    private function getUrlByAddress($address)
    {
        return $this->googleServiceUrl.'?address='.urlencode($address).'&key='.$this->apiKey;
    }


    public function getGeocode($address)
    {
        
        $request = $this->requestStack->getMasterRequest();
        $referer = 'localhost';
        if ($request) {
            $referer  = $request->headers->get('referer');
        }
        $data = file_get_contents($this->getUrlByAddress($address));
        if( $data = json_decode($data, true) ){
            if(isset($data['results']) && $data['status'] == 'OK' )
                return $data['results'];
        }
        return false;
    }
    
}

