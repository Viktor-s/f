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
            Events::onFlush,
        ];
    }
    
}