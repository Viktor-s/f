<?php

namespace Furniture\GoogleServicesBundle;

use Furniture\GoogleServicesBundle\DependencyInjection\GoogleServicesExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GoogleServicesBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getContainerExtension()
    {
        if (!$this->extension) {
            $this->extension = new GoogleServicesExtension();
        }

        return $this->extension;
    }
}
