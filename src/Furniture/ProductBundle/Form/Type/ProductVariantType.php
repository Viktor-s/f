<?php

namespace Furniture\ProductBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Sylius\Bundle\CoreBundle\Form\Type\ProductVariantType as BaseProductVariantType;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationMapper;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
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
use Symfony\Component\Validator\ConstraintViolation;

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
                'label'        => 'sylius.form.variant.images',
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
                        },
                    ]);
                }
            });

            // Validation of ProductPartMaterialSelections. Kostyl.
            // @TODO: This should be moved to Entity Constrains.
            $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
                // Validate product variant options.
                $errors = [];
                $form = $event->getForm();


                /** @var \Furniture\ProductBundle\Entity\ProductVariant $variant */
                $variant = $event->getData();

                $allowedProductParts = [];

                foreach ($variant->getProductScheme()->getProductParts() as $productPart) {
                    $allowedProductParts[] = $productPart->getId();
                }

                /** @var ArrayCollection $submittedVariantSelectionsNormalData */
                $submittedVariantSelectionsNormalData = $form->get('productPartVariantSelections')->getNormData();

                if (count($allowedProductParts) > $submittedVariantSelectionsNormalData->count()) {
                    /** @var productPartVariantSelection $selection */
                    foreach ($form->get('productPartVariantSelections')->all() as $key => $ele) {
                        if (!$submittedVariantSelectionsNormalData->containsKey($key)) {
                            $message = 'Product variant options should not be empty.';
                            $propertyPath = sprintf('children[productPartVariantSelections].children[%d]', $key);
                            $vm = new ViolationMapper();
                            // Convert error to violation.
                            $constraint = new ConstraintViolation(
                                $message, $message, [], '', $propertyPath, null
                            );
                            $vm->mapViolation($constraint, $form);
                        }
                    }
                }
            });
            
            $dataCollector = [
                'part' => [],
                'materialVariant' => [],
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
                'product_variant_object' => $variant,
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
                        if (!empty($value)) {
                            list($productPartId, $productPartMaterialVariantId) = explode('_', $value);

                            $value = null;
                            foreach ($variant->getProductPartVariantSelections() as $vs) {
                                if ($vs->getProductPart()->getId() == $productPartId
                                    && $vs->getProductPartMaterialVariant()->getId() == $productPartMaterialVariantId
                                ) {
                                    $value = $vs;
                                }
                            }

                            if (!$value) {
                                $value = new ProductPartVariantSelection();
                                $value->setProductPart($dataCollector['part'][$productPartId]);
                                $value->setProductPartMaterialVariant(
                                    $dataCollector['materialVariant'][$productPartMaterialVariantId]
                                );
                            }

                            $arrCollection->add($value);
                        }
                    }

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
