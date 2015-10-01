<?php

namespace Furniture\ProductBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Form\Type\ProductVariantType as BaseProductVariantType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Furniture\ProductBundle\Form\EventListener\BuildSkuOptionFormSubscriber;
use Symfony\Component\Form\CallbackTransformer;
use Doctrine\Common\Collections\ArrayCollection;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\ProductBundle\Entity\ProductPart;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;
use Furniture\ProductBundle\Entity\ProductPartVariantSelection;

class ProductVariantType extends BaseProductVariantType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);


        if (!$options['master']) {
            /* PISEC PODKRALSA NEZAMETNO ....................................... */
            $variant = $builder->getData();
            $dataCollector = [
                'part' => [],
                'materialVariant' => []
            ];
            foreach ($variant->getProduct()->getProductParts() as $productPart) {
                $dataCollector['part'][$productPart->getId()] = $productPart;
                foreach ($productPart->getProductPartMaterials() as $productPartMaterial) {
                    foreach ($productPartMaterial->getVariants() as $productPartMaterialVariant) {
                        $dataCollector['materialVariant'][$productPartMaterialVariant->getId()] = $productPartMaterialVariant;
                    }
                }
            }


            $builder->add('skuOptions', new ProductVariantSkuOptions($variant));
            $builder->add('productPartVariantSelections', 'ProductVariantPartMaterialsType', [
                'product_varant_object' => $variant
            ]);
            $builder->get('productPartVariantSelections')->addModelTransformer(new CallbackTransformer(
                    function ($selection) {
                $arrCollection = new ArrayCollection();

                foreach ($selection as $value) {
                    $arrCollection->add($value->getProductPart()->getId() . '_' . $value->getProductPartMaterialVariant()->getId());
                }

                return $arrCollection;
            }, function ($selection) use ($variant, $dataCollector) {
                $arrCollection = new ArrayCollection();

                foreach ($selection as $value) {
                    list($productPartId, $productPartMaterialVariantId) = explode('_', $value);

                    $value = null;
                    foreach ($variant->getProductPartVariantSelections() as $vs) {
                        if ($vs->getProductPart()->getId() == $productPartId && $vs->getProductPartMaterialVariant()->getId() == $productPartMaterialVariantId
                        ) {
                            $value = $vs;
                        }
                    }

                    if (!$value) {
                        $value = new ProductPartVariantSelection();
                        $value->setProductPart($dataCollector['part'][$productPartId]);
                        $value->setProductPartMaterialVariant($dataCollector['materialVariant'][$productPartMaterialVariantId]);
                    }

                    $arrCollection->add($value);
                };
                //echo 'C:'.count($arrCollection);die();
                return $arrCollection;
            }
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        parent::setDefaultOptions($resolver);
    }

}
