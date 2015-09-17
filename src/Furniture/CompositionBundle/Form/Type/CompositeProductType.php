<?php

namespace Furniture\CompositionBundle\Form\Type;

use Furniture\CompositionBundle\Entity\Composite;
use Furniture\CompositionBundle\Entity\CompositeCollection;
use Furniture\CompositionBundle\Entity\CompositeProduct;
use Furniture\CompositionBundle\Entity\CompositeTemplateItem;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\Repository\ProductRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompositeProductType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompositeProduct::class
        ]);

        $resolver->setRequired(['template_item', 'collection', 'composite']);
        $resolver->setAllowedTypes('template_item', CompositeTemplateItem::class);
        $resolver->setAllowedTypes('collection', CompositeCollection::class);
        $resolver->setAllowedTypes('composite', Composite::class);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var CompositeTemplateItem $templateItem */
        $templateItem = $options['template_item'];
        $taxon = $templateItem->getTaxon();
        /** @var CompositeCollection $collection */
        $collection = $options['collection'];
        /** @var Composite $composite */
        $composite = $options['composite'];

        $builder
            ->add('product', 'entity', [
                'class' => Product::class,
                'label' => $taxon->getName(),
                'query_builder' => function (ProductRepository $repository) use ($taxon, $collection) {
                    return $repository->createQueryBuilder('p')
                        ->select('p')
                        ->innerJoin('p.taxons', 't')
                        ->innerJoin('p.compositeCollections', 'c')
                        ->andWhere('t.id = :taxon_id')
                        ->andWhere('c.id = :collection_id')
                        ->setParameters([
                            'taxon_id' => $taxon->getId(),
                            'collection_id' => $collection->getId()
                        ]);
                }
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($templateItem, $composite) {
            /** @var CompositeProduct $compositeProduct */
            $compositeProduct = $event->getData();

            $compositeProduct
                ->setTemplateItem($templateItem)
                ->setComposite($composite);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'composite_product';
    }
}
