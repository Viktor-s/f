<?php

namespace Furniture\RetailerBundle\Form\Type;

use Furniture\CommonBundle\Form\Type\BackendImageType;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\GoogleServicesBundle\Api\Maps\Geocoding;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Furniture\RetailerBundle\Entity\RetailerProfileTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Furniture\CommonBundle\Form\DataTransformer\ArrayToStringTransformer;
use Furniture\RetailerBundle\Entity\RetailerProfileLogoImage;

class RetailerProfileType extends AbstractType
{
    /**
     * @var Geocoding
     */
    private $geocoding;

    /**
     * Construct
     *
     * @param Geocoding $geocoding
     */
    public function __construct(Geocoding $geocoding)
    {
        $this->geocoding = $geocoding;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RetailerProfile::class,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', 'text', [
                'label'  => 'Address',
                'mapped' => false,
                'attr'   => [
                    'class' => 'address-autocomplete',
                ],
            ])
            ->add('addressLatitude', 'hidden', [
                'mapped' => false,
                'attr' => [
                    'data-address-latitude' => true,
                ],
            ])
            ->add('addressLongitude', 'hidden', [
                'mapped' => false,
                'attr' => [
                    'data-address-longitude' => true,
                ],
            ])
            ->add('addressReplace', 'checkbox', [
                'label' => 'Replace address',
                'mapped' => false,
                'data' => false,
            ])
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new RetailerProfileTranslationType(),
            ])
            ->add('name', 'text', [
                'required' => true,
            ])
            ->add('website', 'text', [
                'required' => false,
            ])
            ->add('subtitle', 'text', [
                'required' => false,
            ])
            ->add('description', 'textarea', [
                'required' => false,
            ])
            ->add('logoImage', new BackendImageType(RetailerProfileLogoImage::class), [
                'required' => false,
            ])
            ->add('phones', 'text', [
                'label'    => 'furniture_retailer_profile.form.phones',
                'required' => false,
            ])
            ->add('emails', 'text', [
                'label'    => 'furniture_retailer_profile.form.emails',
                'required' => false,
            ])
            ->add('demoFactories', 'entity', [
                'label'    => 'furniture_retailer_profile.form.demo_factories',
                'class'    => Factory::class,
                'multiple' => true,
                'expanded' => false,
            ]);

        $builder->get('phones')->addModelTransformer(new ArrayToStringTransformer(','));
        $builder->get('emails')->addModelTransformer(new ArrayToStringTransformer(','));

        // We should process on presubmit, because "a2lix_translationsForms" set required false and control is empty
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (isset($data['addressReplace']) && $data['addressReplace']) {
                $latitude = isset($data['addressLatitude']) ? $data['addressLatitude'] : null;
                $longitude = isset($data['addressLongitude']) ? $data['addressLongitude'] : null;

                if ($latitude && $longitude) {
                    foreach ($data['translations'] as $locale => $translation) {
                        $parts = explode('_', $locale);
                        $language = $parts[0];

                        $address = $this->geocoding->getAddressByGeoPosition($latitude, $longitude, $language);
                        $data['translations'][$locale]['address'] = $address;
                    }
                } else {
                    foreach ($data['translations'] as $locale => $translation) {
                        $data['translations'][$locale]['address'] = null;
                    }
                }
            }

            $event->setData($data);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'furniture_retailer_profile';
    }
}
