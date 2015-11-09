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

        if (!$user->getRetailerProfile()) {
            // Only for retailers granted
            return false;
        }

        if (!$user->isRetailerAdmin()) {
            // Only for admin granted
            return false;
        }

        if (!$object || !$object instanceof User) {
            return false;
        }

        $retailerProfile = $user->getRetailerProfile();

        if ($retailerProfile->hasUser($object)) {
            return true;
        }

        return false;
    }
}
