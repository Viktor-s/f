<?php

namespace Furniture\SpecificationBundle\Security\Voter;

use Furniture\SpecificationBundle\Entity\CustomSpecificationItem;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

class CustomSpecificationItemVoter extends AbstractVoter
{
    /**
     * {@inheritDoc}
     */
    protected function getSupportedClasses()
    {
        return [CustomSpecificationItem::class];
    }

    /**
     * {@inheritDoc}
     */
    protected function getSupportedAttributes()
    {
        return ['CREATE', 'EDIT', 'REMOVE'];
    }

    /**
     * {@inheritDoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        /** @var \Furniture\CommonBundle\Entity\User $user */
        if (!$user) {
            return false;
        }

        if ($user->isNoRetailer()) {
            // Only retailer have grants to custom specification item.
            return false;
        }

        if ($attribute == 'CREATE') {
            return true;
        }

        /** @var CustomSpecificationItem $object */
        $owner = $object->getSpecificationItem()->getSpecification()->getCreator();
        
        if ($user->getRetailerUserProfile()->isRetailerAdmin()
                && $user->getRetailerUserProfile()->getRetailerProfile()->getId() == $owner->getRetailerProfile()->getId()
                ) {
            return true;
        }

        return $user->getRetailerUserProfile()->getId() == $owner->getId();
    }
}
