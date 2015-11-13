<?php

namespace Furniture\FactoryBundle\Form\Type;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\FactoryImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Bundle\CoreBundle\Form\Type\ImageType;
use Furniture\CommonBundle\Form\Type\BackendImageType;
use Furniture\FactoryBundle\Entity\FactoryLogoImage;

class FactoryType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Factory::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new FactoryTranslationType()
            ])
            ->add('name', 'text', [
                'required' => true
            ])
            ->add('images', 'collection', [
                'type' => new ImageType(FactoryImage::class),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('logoImage', new BackendImageType(FactoryLogoImage::class), [
                'required' => false
            ])
            ->add('contacts', 'collection', [
                'type' => new FactoryContactType(),
                'allow_add' => true,
                'allow_delete' => true
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var Factory $factory */
            $factory = $event->getData();

            foreach ($factory->getContacts() as $contact) {
                $contact->setFactory($factory);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'furniture_factorybundle_factory';
    }
}
