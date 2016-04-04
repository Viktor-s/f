<?php

namespace Furniture\RetailerBundle\Security\Voter;

use Furniture\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class RetailerPartnersVoter implements VoterInterface
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

        if (in_array('RETAILER_PARTNERS_LIST', $attributes) || in_array('RETAILER_PARTNERS_VIEW', $attributes)) {
            if ($user->isRetailer() && ($user->getRetailerUserProfile()->isRetailerAdmin()
                || $user->getRetailerUserProfile()->isRetailerEmployee()) || $user->isFactoryAdmin()
            ) {
                return self::ACCESS_GRANTED;
            }

            return self::ACCESS_DENIED;
        }

        return self::ACCESS_ABSTAIN;
    }
}
