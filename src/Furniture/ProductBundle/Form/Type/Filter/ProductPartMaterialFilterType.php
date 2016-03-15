<?php

namespace Furniture\ProductBundle\Form\Type\Filter;

use Furniture\FactoryBundle\Entity\Factory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductPartMaterialFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'required' => false,
                'label'    => 'product_part_material.form.name',
                'attr'     => array(
                    'placeholder' => 'product_part_material.form.name'
                )
            ))
            ->add('factory', 'entity', [
                'class'    => Factory::class,
                'required' => false,
                'label'    => 'product_part_material.form.factory',
                'attr'     => [
                    'placeholder' => 'product_part_material.form.factory',
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'product_part_material_filter';
    }
}