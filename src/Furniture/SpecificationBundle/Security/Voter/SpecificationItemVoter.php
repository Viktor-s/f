<?php

namespace Furniture\SpecificationBundle\Security\Voter;

use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

class SpecificationItemVoter extends AbstractVoter
{
    /**
     * {@inheritDoc}
     */
    protected function getSupportedClasses()
    {
        return [SpecificationItem::class];
    }

    /**
     * {@inheritDoc}
     */
    protected function getSupportedAttributes()
    {
        return ['VIEW', 'CREATE', 'EDIT', 'REMOVE'];
    }

    /**
     * {@inheritDoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        
        /** @var \Furniture\CommonBundle\Entity\User $user */
        if ($user->isNoRetailer()) {
            // Only retailer have grants to custom specification item.
            return false;
        }

        if ($attribute == 'CREATE') {
            return true;
        }

        if(!$object instanceof SpecificationItem)
            return false;
        
        /** @var SpecificationItem $object */
        $owner = $object->getSpecification()->getCreator();
        
        if ($user->getRetailerUserProfile()->isRetailerAdmin()
                && $user->getRetailerUserProfile()->getRetailerProfile()->getId() == $owner->getRetailerProfile()->getId()
                ) {
            return true;
        }

        if (!$user) {
            return false;
        }

        return $user->getRetailerUserProfile()->getId() == $owner->getId();
    }
}
