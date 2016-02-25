<?php

namespace Furniture\ProductBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Sylius\Bundle\CoreBundle\Form\Type\ProductVariantType as BaseProductVariantType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\CallbackTransformer;
use Doctrine\Common\Collections\ArrayCollection;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\ProductBundle\Entity\ProductPart;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;
use Furniture\ProductBundle\Entity\ProductPartVariantSelection;
use Furniture\ProductBundle\Entity\ProductScheme;

class ProductVariantType extends BaseProductVariantType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // Replace images
        $builder
            ->remove('images')
            ->add('images', 'collection', [
                'type'         => new ProductVariantImageType(),
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label'        => 'sylius.form.variant.images'
            ]);

        if (!$options['master']) {
            /* PISEC PODKRALSA NEZAMETNO ....................................... */
            $variant = $builder->getData();
            
            $builder->add('factoryCode');
            
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                /** @var \Furniture\ProductBundle\Entity\ProductVariant $variant */
                $variant = $event->getData();
                $product = $variant->getProduct();

                $disabled = (bool) $variant->getId();

                if ($variant->getProduct()->isSchematicProductType()) {
                    $event->getForm()->add('productScheme', 'entity', [
                        'class' => ProductScheme::class,
                        'property' => 'name',
                        'disabled' => $disabled,
                        'query_builder' => function (EntityRepository $er) use ($product) {
                            return $er->createQueryBuilder('ps')
                                ->andWhere('ps.product = :product')
                                ->setParameter('product', $product);
                        }
                    ]);
                }
            });
            
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

            $callbackTransformer = new CallbackTransformer(
                function ($selection) {
                    $arrCollection = new ArrayCollection();

                    foreach ($selection as $value) {
                        $arrCollection->add($value->getProductPart()->getId() . '_' . $value->getProductPartMaterialVariant()->getId());
                    }

                    return $arrCollection;
                },

                function ($selection) use ($variant, $dataCollector) {
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

                    return $arrCollection;
                }
            );

            $builder->get('productPartVariantSelections')->addModelTransformer($callbackTransformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
    }
}
