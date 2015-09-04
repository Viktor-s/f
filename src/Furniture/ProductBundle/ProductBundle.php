<?php

namespace Furniture\ProductBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ProductBundle extends Bundle
{
    public function getParent() {
        return 'SyliusProductBundle';
    }
}
