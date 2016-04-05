<?php

namespace Furniture\CommonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Persistence\ObjectManager;

class EntityHiddenType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $objectManager = $this->objectManager;
        $class = $options['class'];

        $transformer = new CallbackTransformer(
            function ($entity) {
                return null !== $entity ? $entity->getId() : null;
            },
            function ($id) use ($objectManager, $class) {
                if (!$id) {
                    return null;
                }

                $entity = $objectManager
                    ->getRepository($class)
                    ->find($id);

                if (null === $entity) {
                    throw new TransformationFailedException();
                }

                return $entity;
            }
        );
        $builder->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['class'])
            ->setDefaults([
                'invalid_message' => 'The entity does not exist.',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'hidden';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'entity_hidden';
    }
}
