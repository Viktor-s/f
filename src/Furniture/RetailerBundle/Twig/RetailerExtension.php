<?php

namespace Furniture\RetailerBundle\Twig;

use Furniture\RetailerBundle\RetailerRemoval\RemovalCheckerRegistry;
use Furniture\RetailerBundle\Entity\RetailerProfile;

class RetailerExtension extends \Twig_Extension
{
    /**
     * @var RemovalCheckerRegistry
     */
    private $removalCheckerRegistry;

    /**
     * Construct
     *
     * @param RemovalCheckerRegistry $removalCheckerRegistry
     */
    public function __construct(RemovalCheckerRegistry $removalCheckerRegistry)
    {
        $this->removalCheckerRegistry = $removalCheckerRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            'is_retailer_profile_can_remove' => new \Twig_Function_Method($this, 'isRetailerProfileCanRemove'),
        ];
    }

    /**
     * Is retailer profile can remove?
     *
     * @param RetailerProfile $profile
     *
     * @return bool
     */
    public function isRetailerProfileCanRemove(RetailerProfile $profile)
    {
        return $this->removalCheckerRegistry
            ->getRetailerProfileRemovalChecker()
            ->canHardRemove($profile)
            ->canRemove();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'retialer';
    }
}