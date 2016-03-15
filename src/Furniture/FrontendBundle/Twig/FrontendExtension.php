<?php

namespace Furniture\FrontendBundle\Twig;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FrontendBundle\Menu\FrontendMenuBuilder;
use Knp\Menu\Twig\Helper as KnpMenuHelper;

class FrontendExtension extends \Twig_Extension
{
    /**
     * @var KnpMenuHelper
     */
    private $knpMenuHelper;

    /**
     * @var FrontendMenuBuilder
     */
    private $menuBuilder;

    /**
     * Construct
     *
     * @param KnpMenuHelper       $knpMenuHelper
     * @param FrontendMenuBuilder $menuBuilder
     */
    public function __construct(KnpMenuHelper $knpMenuHelper, FrontendMenuBuilder $menuBuilder)
    {
        $this->knpMenuHelper = $knpMenuHelper;
        $this->menuBuilder = $menuBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            'knp_menu_render_factory_side' => new \Twig_Function_Method($this, 'factorySideMenuRender', ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('format_url', [$this, 'urlFormatter']),
        ];
    }

    /**
     * Render side menu for factory
     *
     * @param Factory $factory
     * @param array   $options
     *
     * @return string
     */
    public function factorySideMenuRender(Factory $factory, array $options)
    {
        $menu = $this->menuBuilder->createFactorySideMenu($factory);

        return $this->knpMenuHelper->render($menu, $options);
    }

    /**
     * Format URL string
     *
     * @param       $url
     * @param array $allowedParts
     *
     * @return string
     */
    public function urlFormatter($url, $parts = [])
    {
        $parts = empty($parts) ? ['scheme', 'host', 'path'] : $parts;

        if ($parsedUrl = parse_url($url)) {
            if ($formatted = $this->createFormatter($parts)) {
                $url = $formatted;
                foreach ($parsedUrl as $part => $value) {
                    if (in_array($part, $parts)) {
                        $this->replaceComponent($value, $part, $url);
                    }
                }
            }
        }

        return $url;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'furniture_frontend';
    }

    /**
     * Helper function to create url formatter.
     *
     * @param $urlParts
     *
     * @return string
     */
    private function createFormatter($urlParts)
    {
        $url = '';
        $single = count($urlParts) == 1;

        if (in_array('scheme', $urlParts)) {
            $url .= !$single ? ':scheme:://' : ':scheme:';
        }

        if (in_array('user', $urlParts)) {
            $url .= ':user:';
        }

        if (in_array('pass', $urlParts)) {
            if (!$single && in_array('user', $urlParts)) {
                $url .= '::pass:';
            }
            else if ($single){
                $url .= ':pass:';
            }
        }

        if (!$single && in_array('user', $urlParts)) {
            $url .= '@';
        }
        if (in_array('host', $urlParts)) {
            $url .= ':host:';
        }

        if (in_array('port', $urlParts)) {
            $url .= !$single ? '::port:' : ':port:';
        }

        if (in_array('path', $urlParts)) {
            $url .= ':path:';
        }

        if (in_array('query', $urlParts)) {
            $url .= !$single ? '?:query:' : ':query:';
        }

        if (in_array('fragment', $urlParts)) {
            $url .= !$single ? '#:fragment:' : ':fragment:';
        }

        return $url;
    }

    /**
     * Helper function to replace url formatter components.
     *
     * @param $value
     * @param $component
     * @param $url
     *
     * @return mixed
     */
    private function replaceComponent($value, $component, &$url)
    {
        $url = str_replace(":$component:", $value, $url);

        return $url;
    }
}
