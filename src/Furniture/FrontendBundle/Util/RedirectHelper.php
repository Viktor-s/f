<?php

namespace Furniture\FrontendBundle\Util;

use Symfony\Component\HttpFoundation\Request;

final class RedirectHelper
{
    /**
     * Get the redirect URL
     *
     * @param Request $request
     * @param string  $default
     *
     * @return string
     */
    public static function getRedirectUrl(Request $request, $default)
    {
        if ($request->query->has('_from')) {
            return $request->query->get('_from');
        }

        return $default;
    }
}