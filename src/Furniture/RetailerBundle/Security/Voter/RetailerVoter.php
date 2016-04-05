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
        return ['RETAILER_EDIT', 'RETAILER_VIEW'];
    }

    /**
     * {@inheritDoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        if (!$user || !$user instanceof User) {
            return false;
        }

        if (!$object || !$object instanceof RetailerProfile) {
            return false;
        }

        if(!$user->isRetailer()){
            // Only for retailers granted
            return false;
        }

        // Is owner of profile
        if ($object->getId() == $user->getRetailerUserProfile()->getRetailerProfile()->getId()) {
            switch ($attribute) {
                case 'RETAILER_EDIT':
                    if ($user->getRetailerUserProfile()->isRetailerAdmin()) {
                        return true;
                    }
                    break;
                case 'RETAILER_VIEW':
                    if ($user->getRetailerUserProfile()->isRetailerAdmin()
                        || $user->getRetailerUserProfile()->isRetailerEmployee()
                    ) {
                        return true;
                    }
                    break;
            }
        }

        return false;
    }
}
