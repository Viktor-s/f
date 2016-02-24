<?php

namespace Furniture\UserBundle\Controller;

use Furniture\UserBundle\Entity\Customer;
use Furniture\UserBundle\Form\Type\CustomerType;
use Furniture\UserBundle\Form\Type\Retailer\CustomerType as RetailerCustomerType;
use Sylius\Bundle\CoreBundle\Controller\CustomerController as BaseCustomerController;
use Sylius\Component\Resource\Event\ResourceEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerController extends BaseCustomerController
{
    /**
     * {@inheritDoc}
     */
    public function getForm($resource = null, array $options = [])
    {
        $request = $this->get('request');

        if ($request->query->get('mode') == 'retailer') {
            $form = new RetailerCustomerType();
        } else {
            $form = new CustomerType();
        }

        return $this->createForm($form, $resource);
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
    public function createAction(Request $request)
    {
        $this->isGrantedOr403('create');

        $resource = $this->createNew();
        $form = $this->getForm($resource);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            // We edit user in administration, and the user not should verify email
            $resource->__disableVerifyEmail = true;
            $resource = $this->domainManager->create($form->getData());

            if ($resource instanceof ResourceEvent) {
                return $this->redirectHandler->redirectToIndex();
            }

            return $this->redirectHandler->redirectTo($resource);
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView(),
            ))
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

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH')) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            // We edit user in administration, and the user not should verify email
            $resource->__disableVerifyEmail = true;
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

    /**
     * Reset password for customer
     *
     * @param integer $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function resetPassword($id)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');

        $customer = $this->get('doctrine.orm.default_entity_manager')
            ->find(Customer::class, $customerId = $id);

        if (!$customer) {
            throw new NotFoundHttpException(sprintf(
                'Not found customer with identifier "%s".',
                $customerId
            ));
        }

        /** @var \Furniture\UserBundle\Entity\User $user */
        $user = $customer->getUser();
        $user->setNeedResetPassword(true);

        $em->flush($user);

        $this->flashHelper->setFlash('info', 'Successfully set flag for reset user password.');

        $toUrl = $this->get('router')->generate('sylius_backend_customer_index');

        return new RedirectResponse($toUrl);
    }
}
