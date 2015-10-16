<?php

namespace Furniture\CommonBundle\Templating;

use Sylius\Bundle\CoreBundle\Templating\Helper\MoneyHelper as BaseMoneyHelper;

class MoneyHelper extends BaseMoneyHelper
{
    /**
     * {@inheritDoc}
     */
    public function formatAmount($amount, $currency = null, $decimal = false, $locale = null)
    {
        $currency = $currency ?: $this->getDefaultCurrency();

        // Fix for #98
        if ($currency == 'EUR') {
            return sprintf('%s €', round($amount / 100, 2));
        }

        return parent::formatAmount($amount, $currency, $decimal, $locale); // TODO: Change the autogenerated stub
    }
}
