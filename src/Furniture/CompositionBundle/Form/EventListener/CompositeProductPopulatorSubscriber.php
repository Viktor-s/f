<?php

namespace Furniture\CompositionBundle\Form\EventListener;

use Furniture\CompositionBundle\Entity\Composite;
use Furniture\CompositionBundle\Form\Type\CompositeProductType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CompositeProductPopulatorSubscriber implements EventSubscriberInterface
{
    /**
     * @var Composite
     */
    private $composite;

    /**
     * Construct
     *
     * @param Composite $composite
     */
    public function __construct(Composite $composite)
    {
        $this->composite = $composite;
    }

    /**
     * Populate form for product
     *
     * @param FormEvent $event
     */
    public function populateForProduct(FormEvent $event)
    {
        $form = $event->getForm();
        $template = $this->composite->getTemplate();
        $collection = $template->getCollection();

        $index = 0;

        foreach ($template->getItems() as $item) {
            for ($i = 0; $i < $item->getCount(); $i++) {
                $form->add($index, new CompositeProductType(), [
                    'template_item' => $item,
                    'collection' => $collection,
                    'composite' => $this->composite
                ]);

                $index++;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'populateForProduct'
        ];
    }
}
