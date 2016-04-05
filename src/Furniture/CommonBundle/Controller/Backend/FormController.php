<?php

namespace Furniture\CommonBundle\Controller\Backend;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Form\Type\FactoryRetailerRelationFilterType;
use Furniture\ProductBundle\Form\Type\Filter\ProductPartMaterialFilterType;
use Furniture\RetailerBundle\Form\Type\RetailerProfileFilterType;
use Furniture\SpecificationBundle\Form\Type\SpecificationFilterType;
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

    /**
     * Create a product filter form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function productPartMaterialFilterAction()
    {
        $requestStack = $this->get('request_stack');
        $request = $requestStack->getMasterRequest();
        $data = $request->get('criteria');

        $em = $this->getDoctrine()->getManager();

        if (!empty($data['factory'])) {
            $data['factory'] = $em->find(Factory::class, $data['factory']);
        }

        $form = $this->get('form.factory')->createNamed('criteria', new ProductPartMaterialFilterType(), $data);

        return $this->render('SyliusWebBundle:Backend/ProductPartMaterial:filterForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Create a retailer filter form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function retailerProfileFilterAction()
    {
        $requestStack = $this->get('request_stack');
        $request = $requestStack->getMasterRequest();
        $data = $request->get('criteria');

        $form = $this->get('form.factory')->createNamed('criteria', new RetailerProfileFilterType(), $data);

        return $this->render('WebBundle:Backend/RetailerProfile:filterForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Create a specification filter form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function specificationFilterAction()
    {
        $requestStack = $this->get('request_stack');
        $request = $requestStack->getMasterRequest();
        $data = $request->get('criteria');

        $form = $this->get('form.factory')->createNamed('criteria', new SpecificationFilterType(), $data);

        return $this->render('WebBundle:Backend/Specification:filterForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Create a factories retailers relations filter form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function factoriesRetailersRelationsFilterAction()
    {
        $requestStack = $this->get('request_stack');
        $request = $requestStack->getMasterRequest();
        $data = $request->get('criteria');
        $em = $this->getDoctrine()->getManager();
        $form = $this->get('form.factory')->createNamed('criteria', new FactoryRetailerRelationFilterType($em), $data);

        return $this->render('WebBundle:Backend/FactoriesRetailersRelations:filterForm.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
