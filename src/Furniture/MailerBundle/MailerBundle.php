<?php

namespace Furniture\MailerBundle;

use Furniture\MailerBundle\DependencyInjection\MailerExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MailerBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getContainerExtension()
    {
        if (!$this->extension) {
            $this->extension = new MailerExtension();
        }

        return $this->extension;
    }
}
