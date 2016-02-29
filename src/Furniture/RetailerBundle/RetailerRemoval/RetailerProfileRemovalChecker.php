<?php

namespace Furniture\RetailerBundle\RetailerRemoval;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\RemovalChecker\Removal;
use Furniture\RetailerBundle\Entity\RetailerProfile;

class RetailerProfileRemovalChecker
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Can remove retailer profile?
     *
     * @param RetailerProfile $profile
     *
     * @return Removal
     */
    public function canHardRemove(RetailerProfile $profile)
    {
        $reasonMessages = [];

        if ($profile->hasRetailerUserProfiles()) {
            $reasonMessages[] = 'Has references to user.';
        }

        if (count($reasonMessages)) {
            return new Removal(false, $reasonMessages);
        }

        return new Removal(true);
    }
}
