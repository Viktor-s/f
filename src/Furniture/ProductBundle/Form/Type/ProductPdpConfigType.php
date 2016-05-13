<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductPdpConfig;
use Furniture\ProductBundle\Entity\ProductPdpInput;
use Furniture\SkuOptionBundle\Entity\SkuOptionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPdpConfigType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductPdpConfig::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('inputs', 'collection', [
            'type' => new ProductPdpInputType(),
            'allow_delete' => true,
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var ProductPdpConfig $data */
            $data = $event->getData();
            /** @var Product $product */
            $product = $data->getProduct();

            /** Delete input for scheme if product is simple. */
            if ($product->isSimpleProductType()) {
                $data->removeInput($data->getInputForSchemes());
            }

            // Delete inputs for SkuOptions that not used anymore.
            $poorData = $data->getInputs()
                ->filter(
                    function($element) use ($product) {
                        /** @var ProductPdpInput $element */
                        if ($element->getSkuOption()) {
                            return !$product->getSkuOptionTypes()->contains($element->getSkuOption());
                        }

                        return false;
                    }
                )
                ->forAll(
                    function($key, $element) use ($data) {
                        $data->removeInput($element);
                    }
                );
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_pdp_config';
    }
}
