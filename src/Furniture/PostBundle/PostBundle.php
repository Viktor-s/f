<?php

namespace Furniture\PostBundle;

use Furniture\PostBundle\DependencyInjection\PostExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PostBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getContainerExtension()
    {
        if (!$this->extension) {
            $this->extension = new PostExtension();
        }

        return $this->extension;
    }
}
