<?php

namespace Furniture\RetailerBundle\Security\Voter;

use Furniture\CommonBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

class RetailerEmployeeVoter extends AbstractVoter
{
    /**
     * {@inheritDoc}
     */
    protected function getSupportedClasses()
    {
        return [User::class];
    }

    /**
     * {@inheritDoc}
     */
    protected function getSupportedAttributes()
    {
        return ['RETAILER_EMPLOYEE_REMOVE', 'RETAILER_EMPLOYEE_EDIT'];
    }

    /**
     * {@inheritDoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        if (!$user || !$user instanceof User) {
            return false;
        }

        if (!$user->isRetailer()) {
            // Only for retailers granted
            return false;
        }

        if (!$user->getRetailerUserProfile()->isRetailerAdmin()) {
            // Only for admin granted
            return false;
        }

        if (!$object || !$object instanceof User || !$object->isRetailer()) {
            return false;
        }

        $retailerProfile = $user->getRetailerUserProfile()->getRetailerProfile();

        if ( $retailerProfile->hasRetailerUserProfile($object->getRetailerUserProfile())) {
            return true;
        }

        return false;
    }
}
