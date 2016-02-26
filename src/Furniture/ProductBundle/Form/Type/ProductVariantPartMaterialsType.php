<?php

namespace Furniture\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Furniture\ProductBundle\Form\DataTransformer\ProductPartVariantMaterialVariantSelectionTransformer;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ProductVariantPartMaterialsType extends AbstractType
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'product_variant_object',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Furniture\ProductBundle\Entity\ProductVariant $productVariant */
        $productVariant = $options['product_variant_object'];

        $defaultValues = [];
        $contains = [];
        foreach ($productVariant->getProductPartVariantSelections() as $ppvs) {
            $contains[$ppvs->getProductPartMaterialVariant()->getId()] = $ppvs->getProductPartMaterialVariant();
            $defaultValues[] = $ppvs->getProductPart()->getId() . '_' . $ppvs->getProductPartMaterialVariant()->getId();
        }

        $i = 0;

        /** @var \Furniture\ProductBundle\Entity\Product $product */
        $product = $productVariant->getProduct();

        foreach ($product->getProductParts() as $productPart) {
            $productPartMaterialsVariants = [];
            $choiceLabels = [];
            $choiceValues = [];

            foreach ($productPart->getProductPartMaterials() as $productPartMaterial) {
                foreach ($productPartMaterial->getVariants() as $productPartMaterialVariant) {
                    $productPartMaterialsVariants[$productPartMaterialVariant->getName()] = $productPartMaterialVariant->getId();
                    $choiceLabels[] = $productPartMaterialVariant->getName();
                    $choiceValues[] = $productPart->getId() . '_' . $productPartMaterialVariant->getId();
                }
            }

            if (count($productPartMaterialsVariants) > 0) {
                $choiceList = new ChoiceList($choiceValues, $choiceLabels);
                $values = array_intersect($choiceList->getChoices(), $defaultValues);

                $builder->add($i, 'choice', [
                    'label'       => $productPart->getLabel(),
                    'choice_list' => $choiceList,
                    'required'    => false,
                    'attr'        => [
                        'data-part-id' => $productPart->getId(),
                    ],
                    'data' => array_shift($values),
                ]);

                $i++;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ProductVariantPartMaterialsType';
    }

}

