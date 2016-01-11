<?php

namespace Furniture\UserBundle\Controller;

use Sylius\Bundle\CoreBundle\Controller\CustomerController as BaseCustomerController;
use Sylius\Component\Resource\Event\ResourceEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

    /**
     * {@inheritDoc}
     */
    public function updateAction(Request $request)
    {
        $this->isGrantedOr403('update');

        /** @var \Sylius\Component\Core\Model\Customer $resource */
        $resource = $this->findOr404($request);
        $form     = $this->getForm($resource);

        $resource->getUser()
            ->shouldControlForKill();

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH')) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            $this->domainManager->update($resource);

            if ($resource instanceof ResourceEvent) {
                return $this->redirectHandler->redirectToIndex();
            }

            return $this->redirectHandler->redirectTo($resource);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form, 400));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('update.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView(),
            ))
        ;

        return $this->handleView($view);
    }


}
