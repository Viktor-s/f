<?php

namespace Furniture\RetailerBundle\RetailerRemoval;

use Symfony\Component\DependencyInjection\ContainerInterface;

class RemovalCheckerRegistry
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Construct
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get product part type removal checker
     *
     * @return RetailerProfileRemovalChecker
     */
    public function getRetailerProfileRemovalChecker()
    {
        return $this->container->get('retailer_profile.removal_checker');
    }
}
