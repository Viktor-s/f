<?php

namespace Furniture\FrontendBundle\Form\Type;

use Furniture\CommonBundle\Form\DataTransformer\ArrayToStringTransformer;
use Furniture\CommonBundle\Form\Type\BackendImageType;
use Furniture\GoogleServicesBundle\Api\Maps\Geocoding;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Furniture\RetailerBundle\Entity\RetailerProfileLogoImage;
use Furniture\RetailerBundle\Form\Type\RetailerProfileTranslationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            'data_class'        => RetailerProfile::class,
            'validation_groups' => ['RetailerProfileCreate'],
            'registration'      => false,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var RetailerProfile|null $retailerProfile */
        $retailerProfile = $builder->getData();

        $builder
            ->add('name', 'text', [
                'required' => true,
                'label'    => $options['registration'] ? 'frontend.company_name' : 'frontend.name',
            ])
            ->add('website', 'text', [
                'required' => false,
                'label'    => $options['registration'] ? 'frontend.register.website' : 'frontend.website',
            ]);
    if (!$options['registration']) {
        $builder
            ->add('subtitle', 'text', [
                'required' => false,
                'label'    => 'frontend.subtitle',
            ])
            ->add('description', 'textarea', [
                'required' => false,
                'label'    => 'frontend.description',
            ]);
    }
        $builder
            ->add('address', 'text', [
                'label'    => 'Address',
                'mapped'   => false,
                'attr'     => [
                    'class' => 'address-autocomplete',
                ],
                'data'     => $retailerProfile ? $retailerProfile->getAddress() : '',
                'required' => false,
            ])
            ->add('phones', 'text', [
                'label'    => $options['registration'] ? 'frontend.register.contact_phones' : 'frontend.phones_contact',
                'required' => false,
            ])
            ->add('emails', 'text', [
                'label'    => $options['registration'] ? 'frontend.register.contact_emails' : 'frontend.emails_contact',
                'required' => false,
            ])
            ->add('addressLatitude', 'hidden', [
                'mapped' => false,
                'attr'   => [
                    'data-address-latitude' => true,
                ],
            ])
            ->add('addressLongitude', 'hidden', [
                'mapped' => false,
                'attr'   => [
                    'data-address-longitude' => true,
                ],
            ]);
        if ($options['registration']) {
            $builder->add('logoImage', new BackendImageType(RetailerProfileLogoImage::class), [
                'required' => false,
            ]);
        }

        $builder->get('phones')->addModelTransformer(new ArrayToStringTransformer(','));
        $builder->get('emails')->addModelTransformer(new ArrayToStringTransformer(','));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            $form->add('translations', 'a2lix_translationsForms', [
                'form_type' => new RetailerProfileTranslationType(),
            ]);

            $translationsData = $form->get('translations')->all();

            $latitude = isset($data['addressLatitude']) ? $data['addressLatitude'] : null;
            $longitude = isset($data['addressLongitude']) ? $data['addressLongitude'] : null;

            $key = 'address';
            if ($latitude && $longitude) {
                foreach ($translationsData as $locale => $formEle) {
                    if (array_key_exists($key, $formEle->all())) {
                        $parts = explode('_', $locale);
                        $language = $parts[0];

                        $address = $this->geocoding->getAddressByGeoPosition($latitude, $longitude, $language);
                        $data['translations'][$locale][$key] = $address;
                    }
                }
            } else if (!empty($data[$key])) {
                foreach ($translationsData as $locale => $formEle) {
                    if (array_key_exists($key, $formEle->all())) {
                        $data['translations'][$locale][$key] = $data[$key];
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
        return 'furniture_retailer_profile_frontend';
    }
}
