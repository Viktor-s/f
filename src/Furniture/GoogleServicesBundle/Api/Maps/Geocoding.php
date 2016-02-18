<?php
namespace Furniture\GoogleServicesBundle\Api\Maps;

use Symfony\Component\HttpFoundation\RequestStack;

class Geocoding
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $googleServiceUrl = 'https://maps.googleapis.com/maps/api/geocode/json';

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Gonstructor.
     *
     * @param string       $apiKey
     * @param RequestStack $requestStack
     */
    public function __construct($apiKey, RequestStack $requestStack)
    {
        $this->apiKey = $apiKey;
        $this->requestStack = $requestStack;
    }

    /**
     * Get URL by address
     *
     * @param $address
     *
     * @return string
     */
    private function getUrlByAddress($address)
    {
        return $this->googleServiceUrl . '?language=en&address=' . urlencode($address) . '&key=' . $this->apiKey;
    }

    /**
     * Get geo code by address
     *
     * @param string $address
     *
     * @return array|null
     */
    public function getGeocode($address)
    {
        $data = file_get_contents($this->getUrlByAddress($address));

        if ($data = json_decode($data, true)) {
            if (isset($data['results']) && $data['status'] == 'OK') {
                return $data['results'];
            }
        }

        return null;
    }

    /**
     * Get address by geo position and language
     *
     * @param float  $latitude
     * @param float  $longitude
     * @param string $language
     *
     * @return string
     */
    public function getAddressByGeoPosition($latitude, $longitude, $language)
    {
        $parameters = [
            'latlng' => sprintf('%s,%s', $latitude, $longitude),
            'key' => $this->apiKey,
            'language' => $language
        ];

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($parameters);

        $content = file_get_contents($url);

        $data = json_decode($content, true);

        if ($data && isset($data['status']) && $data['status'] === 'OK' &&
            isset($data['results']) && is_array($data['results']) && count($data['results'])
        ) {
            $results = $data['results'];
            $result = array_shift($results);

            return $result['formatted_address'];
        }

        return null;
    }
}
