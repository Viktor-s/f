<?php

namespace Furniture\CommonBundle\Controller;

use Sylius\Bundle\CoreBundle\Controller\CustomerController as BaseCustomerController;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends BaseCustomerController
{
    /**
     * {@inheritDoc}
     */
    public function getForm($resource = null, array $options = [])
    {
        $options = [];

        $request = $this->get('request');
        if ($request->query->get('mode') == 'retailer') {
            $options['mode'] = 'retailer';
        }

        return parent::getForm($resource, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function indexAction(Request $request)
    {
        $this->isGrantedOr403('index');

        $criteria = $this->config->getCriteria();
        $sorting = $this->config->getSorting();

        $repository = $this->getRepository();

        $resources = $this->resourceResolver->getResource(
            $repository,
            'createPaginator',
            array($criteria, $sorting)
        );
        $resources->setCurrentPage($request->get('page', 1), true, true);
        $resources->setMaxPerPage($this->config->getPaginationMaxPerPage());

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('index.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData($resources)
        ;

        return $this->handleView($view);
    }
}
