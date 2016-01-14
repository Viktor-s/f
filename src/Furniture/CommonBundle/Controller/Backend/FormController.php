<?php

namespace Furniture\CommonBundle\Controller\Backend;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\ProductBundle\Entity\Readiness;
use Sylius\Bundle\WebBundle\Controller\Backend\FormController as BaseFormController;

class FormController extends BaseFormController
{
    /**
     * Render filter form.
     *
     * @param string $type
     * @param string $template
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function filterAction($type, $template)
    {
        $requestStack = $this->get('request_stack');
        $request = $requestStack->getMasterRequest();
        $data = $request->get('criteria');

        $form = $this->get('form.factory')->createNamed('criteria', $type, $data);

        return $this->render($template, [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Create a product filter form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function productFilterAction()
    {
        $requestStack = $this->get('request_stack');
        $request = $requestStack->getMasterRequest();
        $data = $request->get('criteria');

        $em = $this->getDoctrine()->getManager();

        if (!empty($data['factory'])) {
            $data['factory'] = $em->find(Factory::class, $data['factory']);
        }

        if (!empty($data['statuses'])) {
            $data['statuses'] = array_map(function ($statusId) use ($em) {
                return $em->find(Readiness::class, $statusId);
            }, $data['statuses']);

            $data['statuses'] = array_filter($data['statuses']);
        }

        if (empty($data['priceFrom'])) {
            unset ($data['priceFrom']);
        }

        if (empty($data['priceTo'])) {
            unset ($data['priceTo']);
        }

        $form = $this->get('form.factory')->createNamed('criteria', 'sylius_product_filter', $data);

        return $this->render('SyliusWebBundle:Backend/Product:filterForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}