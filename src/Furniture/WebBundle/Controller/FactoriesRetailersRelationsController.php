<?php

namespace Furniture\WebBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\Event\ResourceEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;

use Furniture\FactoryBundle\Form\Type\FactoryRetailerRelationType;

class FactoriesRetailersRelationsController extends ResourceController
{
    protected function getFactoryRelationForm($data, $options)
    {
        return $this->createForm(new FactoryRetailerRelationType(), $data, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function updateAction(Request $request)
    {
        $this->isGrantedOr403('update');

        $resource = $this->findOr404($request);
        $form     = $this->getFactoryRelationForm($resource, [
            'admin_side_access' => true,
        ]);

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH')) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            $this->domainManager->update($resource);

            if ($this->config->isApiRequest()) {
                if ($resource instanceof ResourceEvent) {
                    throw new HttpException($resource->getErrorCode(), $resource->getMessage());
                }

                return $this->handleView($this->view($resource, 204));
            }

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
            ->setData([
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView(),
            ]);

        return $this->handleView($view);
    }
}
