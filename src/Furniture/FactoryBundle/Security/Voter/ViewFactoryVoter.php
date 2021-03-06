<?php

namespace Furniture\FactoryBundle\Security\Voter;

use Furniture\UserBundle\Entity\User;
use Furniture\FactoryBundle\Entity\Factory;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

class ViewFactoryVoter extends AbstractVoter
{
    /**
     * {@inheritDoc}
     */
    protected function getSupportedClasses()
    {
        return [Factory::class];
    }

    /**
     * {@inheritDoc}
     */
    protected function getSupportedAttributes()
    {
        return ['ACTIVE_RELATION'];
    }

    /**
     * {@inheritDoc}
     */
    protected function isGranted($attribute, $factory, $user = null)
    {
        /** @var \Furniture\FactoryBundle\Entity\Factory $factory */
        if (!$user || !$user instanceof User || !$user->isRetailer()
            || $user->getRetailerUserProfile()->getRetailerProfile()->isDemo()
        ) {
            return false;
        }

        switch ($attribute) {
            case 'ACTIVE_RELATION':
                $factoryRetailerRelation = $factory->getRetailerRelationByRetailer(
                    $user
                        ->getRetailerUserProfile()
                        ->getRetailerProfile()
                );

                if ($factory->isEnabled() && (($factoryRetailerRelation
                            && $factoryRetailerRelation->isActive()
                            && $factoryRetailerRelation->isFactoryAccept()
                            && $factoryRetailerRelation->isAccessProducts())
                        || $factory->getDefaultRelation()->isAccessProducts())
                ) {
                    return true;
                }

                return false;

            default:
                return false;
        }
    }
}
