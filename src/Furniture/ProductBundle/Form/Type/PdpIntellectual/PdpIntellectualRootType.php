<?php

namespace Furniture\ProductBundle\Form\Type\PdpIntellectual;

use Furniture\CommonBundle\Form\DataTransformer\ObjectToStringTransformer;
use Furniture\ProductBundle\Entity\PdpIntellectualRoot;
use Furniture\ProductBundle\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PdpIntellectualRootType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PdpIntellectualRoot::class,
            'graph_tree' => null,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Product $product */
        $product = $builder->getData()->getProduct();
        $disallowEdit = $product->hasVariants() || $product->hasProductVariantsPatterns();

        $builder
            ->add('product', 'text', [
                'label'    => 'Product',
                'disabled' => true,
            ])
            ->add('name', 'text', [
                'label'    => 'Name',
                'read_only' => $disallowEdit,
            ])
            ->add('graphJson', 'textarea', [
                'read_only' => true,
            ])
            ->add('tree', 'textarea', [
                'mapped'   => false,
                'data'     => $options['graph_tree'] ? json_encode($options['graph_tree'], JSON_UNESCAPED_UNICODE) : '',
                'required' => true,
                'read_only' => $disallowEdit,
            ]);

        if (!$disallowEdit) {
            $builder->add('submit', 'submit');
        }

        $builder->get('graphJson')
            ->addModelTransformer(new CallbackTransformer(
                function ($array) {
                    return !empty($array) ? json_encode($array) : '';
                },
                function ($json) {
                    return json_decode($json, true);
                }
            ));

        $builder->get('product')->addModelTransformer(new ObjectToStringTransformer());
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'pdp_intellectual_root';
    }
}
