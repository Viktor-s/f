<?php

namespace Furniture\FrontendBundle\Twig;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FrontendBundle\Menu\FrontendMenuBuilder;
use Knp\Menu\Twig\Helper as KnpMenuHelper;
use Furniture\CommonBundle\Util\UrlFormatter;

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
            'knp_menu_render_factory_side' => new \Twig_Function_Method(
                $this,
                'factorySideMenuRender',
                ['is_safe' => ['html']]
            ),
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
     * @param array $parts
     *
     * @return string
     */
    public function urlFormatter($url, $parts = [])
    {
        $formatter = new UrlFormatter($url);

        return $formatter->format($parts);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'furniture_frontend';
    }
}
