<?php

namespace Furniture\FactoryBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Furniture\CommonBundle\Form\DataTransformer\ArrayToStringTransformer;
use Furniture\FactoryBundle\Entity\FactoryContact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactoryContactType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FactoryContact::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new FactoryContactTranslationType()
            ])
            ->add('departmentName', 'text', [
                'label' => 'factory_contact.form.department_name'
            ])
            ->add('address', 'text', [
                'label' => 'factory_contact.form.address'
            ])
            ->add('phones', 'text', [
                'label' => 'factory_contact.form.phones'
            ])
            ->add('emails', 'text', [
                'label' => 'factory_contact.form.emails'
            ])
            ->add('sites', 'text', [
                'label' => 'factory_contact.form.sites'
            ])->add('position', 'integer', [
                'label' => 'factory_contact.form.position'
            ]);

        $builder->get('phones')->addModelTransformer(new ArrayToStringTransformer(','));
        $builder->get('emails')->addModelTransformer(new ArrayToStringTransformer(','));
        $builder->get('sites')->addModelTransformer(new ArrayToStringTransformer(','));

        // Fix for "a2lix_translationsForms". Because it "coder", not control traversable objects.
        $builder->get('translations')->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (is_array($data)) {
                // Convert to array collections
                $data = new ArrayCollection($data);
                $event->setData($data);
            }
        }, 4);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'factory_contact';
    }
}
