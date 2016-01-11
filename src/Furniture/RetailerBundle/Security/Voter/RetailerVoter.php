<?php

namespace Furniture\RetailerBundle\Security\Voter;

use Furniture\UserBundle\Entity\User;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

class RetailerVoter extends AbstractVoter
{
    /**
     * {@inheritDoc}
     */
    protected function getSupportedClasses()
    {
        return [RetailerProfile::class];
    }

    /**
     * {@inheritDoc}
     */
    protected function getSupportedAttributes()
    {
        return ['RETAILER_EDIT'];
    }

    /**
     * {@inheritDoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        if (!$user || !$user instanceof User) {
            return false;
        }

        if(!$user->isRetailer()){
            // Only for retailers granted
            return false;
        }
        
        if (!$user->getRetailerUserProfile()->isRetailerAdmin()) {
            // Only for admin granted
            return false;
        }

        if (!$object || !$object instanceof RetailerProfile) {
            return false;
        }

        if ($object->getId() == $user->getRetailerUserProfile()->getRetailerProfile()->getId()) {
            // Is owner of profile
            return true;
        }

        return false;
    }
}
