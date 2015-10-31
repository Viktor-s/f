<?php

namespace Furniture\SpecificationBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Furniture\SpecificationBundle\Form\DataTransformer\SpecificationIdModelTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class CustomSpecificationItemSingleType extends AbstractType {

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => SpecificationItem::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('specification', 'integer', [
                    'invalid_message' => 'Invalid specification.'
                ])
                ->add('factoryName', 'text', [
                    'property_path' => 'customItem.factoryName',
                    'invalid_message' => 'Invalid factory name.'
                ])
                ->add('name', 'text', [
                    'property_path' => 'customItem.name',
                    'invalid_message' => 'Invalid name.'
                ])
                ->add('price', 'integer', [
                    'property_path' => 'customItem.price',
                    'invalid_message' => 'Invalid price.'
                ])
                ->add('quantity', 'integer')
                ->add('note', 'textarea');

        $builder->get('specification')->addModelTransformer(new SpecificationIdModelTransformer($this->em));
    }

    /**
     * {@inheritDoc}
     */
    public function getName() {
        return 'specification_item_custom';
    }

}
