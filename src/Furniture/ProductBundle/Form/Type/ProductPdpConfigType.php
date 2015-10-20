<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductPdpConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
            'type' => new ProductPdpInputType()
        ]);
    }

    /**
     * Build for for product part
     *
     * @param Product              $product
     * @param FormBuilderInterface $builder
     */
    private function buildFormForProductPart(Product $product, FormBuilderInterface $builder)
    {
        $pdpConfig = $product->getPdpConfig();

        foreach ($product->getProductParts() as $productPart) {
            $input = $pdpConfig->findInputForProductPart($productPart);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_pdp_config';
    }
}
