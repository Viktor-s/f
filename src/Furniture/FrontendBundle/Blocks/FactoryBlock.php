<?php

namespace Furniture\FrontendBundle\Blocks;

use Furniture\FrontendBundle\Repository\FactoryRepository;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FactoryBlock extends BaseBlockService
{
    /**
     * @var FactoryRepository
     */
    private $factoryRepository;

    /**
     * Construct
     *
     * @param FactoryRepository $factoryRepository
     * @param EngineInterface   $templating
     */
    public function __construct(FactoryRepository $factoryRepository, EngineInterface $templating)
    {
        parent::__construct(null, $templating);

        $this->factoryRepository = $factoryRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'limit' => 5,
            'offset' => 0,
            'newest' => false,
            'template' => 'FrontendBundle:Blocks:factories.html.twig'
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();
        $limit = $settings['limit'];
        $offset = $settings['offset'];

        if ($settings['newest']) {
            $factories = $this->factoryRepository->findNewest($limit, $offset);
        } else {
            // @todo: add control filters
            $factories = [];
        }

        return $this->renderResponse($settings['template'], [
            'factories' => $factories
        ]);
    }
}
