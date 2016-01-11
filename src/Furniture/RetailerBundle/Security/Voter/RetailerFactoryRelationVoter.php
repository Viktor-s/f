<?php

namespace Furniture\RetailerBundle\Security\Voter;

use Furniture\UserBundle\Entity\User;
use Furniture\FactoryBundle\Entity\FactoryRetailerRelation;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class RetailerFactoryRelationVoter implements VoterInterface
{
    /**
     * {@inheritDoc}
     */
    public function supportsAttribute($attribute)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $user = $token->getUser();

        if (!$user || !$user instanceof User) {
            return self::ACCESS_ABSTAIN;
        }

        if (in_array('RETAILER_FACTORY_RELATION_LIST', $attributes) || in_array('RETAILER_FACTORY_RELATION_CREATE', $attributes)) {
            if ($user->isRetailer()) {
                if ($user->getRetailerUserProfile()->isRetailerAdmin()) {
                    return self::ACCESS_GRANTED;
                } else {
                    return self::ACCESS_DENIED;
                }
            }

            return self::ACCESS_DENIED;
        }

        if (in_array('RETAILER_FACTORY_RELATION_EDIT', $attributes) || in_array('RETAILER_FACTORY_RELATION_REMOVE', $attributes)) {
            if (!$object instanceof FactoryRetailerRelation) {
                return self::ACCESS_ABSTAIN;
            }

            if ($user->isNoRetailer()) {
                return self::ACCESS_DENIED;
            }

            $retailer = $object->getRetailer();

            if ($retailer->hasRetailerUserProfile($user->getRetailerUserProfile())) {
                return self::ACCESS_GRANTED;
            }

            return self::ACCESS_DENIED;
        }

        return self::ACCESS_ABSTAIN;
    }
}
