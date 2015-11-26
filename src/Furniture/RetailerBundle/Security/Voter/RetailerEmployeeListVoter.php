<?php

namespace Furniture\RetailerBundle\Security\Voter;

use Furniture\CommonBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class RetailerEmployeeListVoter implements VoterInterface
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

        if (!$user instanceof User) {
            return self::ACCESS_ABSTAIN;
        }

        if (!in_array('RETAILER_EMPLOYEE_LIST', $attributes)) {
            return self::ACCESS_ABSTAIN;
        }

        if ($user->isRetailer() && $user->getRetailerUserProfile()->isRetailerAdmin()) {
            return self::ACCESS_GRANTED;
        }

        return self::ACCESS_DENIED;
    }
}
