<?php
namespace Furniture\GoogleServicesBundle\Entity\Subscribers;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Furniture\GoogleServicesBundle\Entity\Interfaces\AddressMarkerInterface;
use Furniture\GoogleServicesBundle\Api\Maps\Geocoding;


class AddressMarkerSubscriber implements EventSubscriber
{
    
    /**
     *
     * @var \Furniture\GoogleServicesBundle\Api\Maps\Geocoding
     */
    private $geocoding;
    
    function __construct( Geocoding $geocoding )
    {
        $this->geocoding = $geocoding;
    }
    
    /**
     * On flush
     *
     * @
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $this->setCoord($em, $entity);
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $this->setCoord($em, $entity);
        }
    }

    /**
     * Upload for entities
     *
     * @param EntityManagerInterface $em
     * @param object                 $entity
     */
    private function setCoord(EntityManagerInterface $em, $entity)
    {
        if ($entity instanceof AddressMarkerInterface) {
            $address = $entity->getAddress();
            $uow = $em->getUnitOfWork();
            
            if($geoData = $this->geocoding->getGeocode($address)){
                $location = $geoData[0]['geometry']['location'];
                $entity->setLat($location['lat']);
                $entity->setLng($location['lng']);
                //echo '<pre>'.print_r($geoData[0]['address_components'], true).'</pre>';die;
                foreach($geoData[0]['address_components'] as $addressComponent){
                    if(in_array( 'route', $addressComponent['types'])){
                        $entity->setRoute($addressComponent['short_name']);
                        continue;
                    }
                    if(in_array( 'country', $addressComponent['types'])){
                        $entity->setCountry($addressComponent['short_name']);
                        continue;
                    }
                    if(in_array( 'street_number', $addressComponent['types'])){
                        $entity->setStreetNumber($addressComponent['short_name']);
                        continue;
                    }
                    if(in_array( 'locality', $addressComponent['types'])){
                        $entity->setLocality($addressComponent['short_name']);
                        continue;
                    }
                    if(in_array( 'sublocality_level_1', $addressComponent['types'])){
                        $entity->setSublocalityLevel1($addressComponent['short_name']);
                        continue;
                    }
                    if(in_array( 'sublocality_level_2', $addressComponent['types'])){
                        $entity->setSublocalityLevel2($addressComponent['short_name']);
                        continue;
                    }
                    if(in_array( 'sublocality_level_3', $addressComponent['types'])){
                        $entity->setSublocalityLevel3($addressComponent['short_name']);
                        continue;
                    }
                    if(in_array( 'sublocality_level_4', $addressComponent['types'])){
                        $entity->setSublocalityLevel4($addressComponent['short_name']);
                        continue;
                    }
                    if(in_array( 'sublocality_level_5', $addressComponent['types'])){
                        $entity->setSublocalityLevel5($addressComponent['short_name']);
                        continue;
                    }
                    if(in_array( 'administrative_area_level_1', $addressComponent['types'])){
                        $entity->setAdministrativeAreaLevel1($addressComponent['short_name']);
                        continue;
                    }
                    if(in_array( 'administrative_area_level_2', $addressComponent['types'])){
                        $entity->setAdministrativeAreaLevel2($addressComponent['short_name']);
                        continue;
                    }
                    if(in_array( 'administrative_area_level_3', $addressComponent['types'])){
                        $entity->setAdministrativeAreaLevel3($addressComponent['short_name']);
                        continue;
                    }
                    if(in_array( 'administrative_area_level_4', $addressComponent['types'])){
                        $entity->setAdministrativeAreaLevel4($addressComponent['short_name']);
                        continue;
                    }
                    if(in_array( 'administrative_area_level_5', $addressComponent['types'])){
                        $entity->setAdministrativeAreaLevel5($addressComponent['short_name']);
                        continue;
                    }
                }
                
                $classMetadata = $em->getClassMetadata(get_class($entity));
                $uow->recomputeSingleEntityChangeSet($classMetadata, $entity);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush
        ];
    }
    
}