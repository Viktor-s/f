<?php

namespace Furniture\ProductBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Sylius\Bundle\CoreBundle\Controller\ProductController as BaseProductController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ProductController extends BaseProductController
{
    /**
     * Variant actions
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function variantGroupEditAction(Request $request)
    {
        // @todo: fix typos
        // $this->isGrantedOr403('variantGrouEdit');

        /** @var \Furniture\ProductBundle\Entity\Product $product */
        $product = $this->findOr404($request);

        $formBuilder = $this->createFormBuilder([]);
        $optionsValues = [];

        foreach ($product->getOptions() as $k => $option) {
            $optionsValues = array_merge(
                    $optionsValues, $option->getValues()->toArray()
            );
        }

        if (count($optionsValues) > 0) {
            $formBuilder->add('options', 'entity', [
                'class' => get_class($optionsValues[0]),
                'label' => 'Option filter',
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function(EntityRepository $er ) use ($optionsValues) {
                    return $er
                                    ->createQueryBuilder('ov')
                                    ->where('ov in (:ovs)')
                                    ->setParameter('ovs', $optionsValues)
                                    ->orderBy('ov.option', 'ASC')
                    ;
                },
                'data' => $optionsValues,
            ]);
        }

        if (count($skuOptionsVariants = $product->getSkuOptionVariants()) > 0) {
            $formBuilder->add('sku_options', 'entity', [
                'class' => get_class($skuOptionsVariants[0]),
                'label' => 'Sku option filter',
                'choice_label' => 'value',
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function(EntityRepository $er) use ($skuOptionsVariants) {
                    return $er
                                    ->createQueryBuilder('sov')
                                    ->where('sov in (:sovs)')
                                    ->setParameter('sovs', $skuOptionsVariants)
                                    ->orderBy('sov.skuOptionType', 'ASC')
                    ;
                },
                'data' => clone $skuOptionsVariants,
            ]);
        }


        /**
         * Common editable values
         */
        $formBuilder->add('price_calculator', 'text', [
            'label' => 'Change cost'
        ]);

        $formBuilder->add('width', 'text', [
            'label' => 'Change width'
        ]);

        $formBuilder->add('height', 'text', [
            'label' => 'Change height'
        ]);

        $formBuilder->add('depth', 'text', [
            'label' => 'Change depth'
        ]);

        $formBuilder->add('weight', 'text', [
            'label' => 'Change weight'
        ]);

        /*
         * Delete items flag
         */
        $formBuilder->add('delete_by_filter', 'submit', [
            'label' => 'Delete this items',
            'attr' => ['class' => 'btn btn-danger btn-md']
        ]);

        $form = $formBuilder->getForm();
        $filteredVariants = [];

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $options = isset($data['options']) ? new ArrayCollection($data['options']) : [];
            $skuOptions = isset($data['sku_options']) ? new ArrayCollection($data['sku_options']->toArray()) : [];
            $deleteAction = $form->get('delete_by_filter')->isClicked();

            $removed = 0;
            /** @var \Furniture\ProductBundle\Entity\ProductVariant $variant */
            foreach ($product->getVariants() as $variant) {

                foreach ($variant->getSkuOptions() as $skuOption) {
                    $callbackForExists = function ($k, $e) use ($skuOption) {
                        return $e->getId() == $skuOption->getId();
                    };

                    if (!$skuOptions->exists($callbackForExists)) {
                        continue 2;
                    }
                }

                foreach ($variant->getOptions() as $option) {
                    if (!$options->contains($option)) {
                        continue 2;
                    }
                }

                if ($deleteAction) {
                    $this->getDoctrine()->getManager()->remove($variant);
                    $removed++;
                    continue;
                }

                $filteredVariants[] = $variant;

                $language = new ExpressionLanguage();

                if ($data['price_calculator'] !== null) {
                    $price = $language->evaluate($data['price_calculator'], [
                        'price' => $variant->getPrice() / 100
                    ]);
                    $variant->setPrice((int) (ceil(($price * 100))));
                }


                if (!is_null($data['width'])) {
                    $value = $language->evaluate($data['width'], [
                        'width' => $variant->getWidth()
                    ]);

                    $variant->setWidth($value);
                }

                if (!is_null($data['height'])) {
                    $value = $language->evaluate($data['height'], [
                        'height' => $variant->getHeight()
                    ]);

                    $variant->setHeight($value);
                }

                if (!is_null($data['depth'])) {
                    $value = $language->evaluate($data['depth'], ['depth' => $variant->getDepth()]);
                    $variant->setDepth($value);
                }

                if (!is_null($data['weight'])) {
                    $value = $language->evaluate($data['weight'], ['weight' => $variant->getWeight()]);
                    $variant->setWeight($value);
                }
            }

            $this->getDoctrine()->getManager()->flush();

            if ($deleteAction) {
                $this->flashHelper->setFlash(
                        'warning', 'Deleted ' . $removed . ' Items'
                );
            } else {
                $this->flashHelper->setFlash(
                        'warning', 'Updated ' . count($filteredVariants) . ' Items'
                );
            }
        }



        $view = $this
                ->view([
                    'product' => $product,
                    'form' => $form->createView(),
                    'updatedVariants' => $filteredVariants
                ])
                ->setTemplate($this->config->getTemplate('variantGroupEdit.html'));

        return $this->handleView($view);
    }

    public function autoCompleteActionNoneBundle(Request $request) {
        $response = [];

        if ($term = (string) $request->get('term')) {
            $products = $this->get('sylius.repository.product')->searchNoneBundleByName($term);

            foreach ($products as $product) {
                $response[] = [
                    'label' => $product->getName(),
                    'value' => $product->getId(),
                ];
            }
        }

        return new JsonResponse($response);
    }

    public function autoCompleteAction(Request $request) {

        $response = [];

        if ($term = (string) $request->get('term')) {
            foreach ($this->get('sylius.repository.product')
                    ->searchByName($term) as $product) {
                $response[] = [
                    'label' => $product->getName(),
                    'value' => $product->getId(),
                ];
            }
        }

        return new JsonResponse($response);
    }

}
