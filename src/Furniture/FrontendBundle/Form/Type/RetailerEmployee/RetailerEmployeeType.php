<?php

namespace Furniture\FrontendBundle\Form\Type\RetailerEmployee;

use Furniture\FrontendBundle\Form\Type\RetailerEmployee\RetailerEmployeeUserProfileType;
use Furniture\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Furniture\RetailerBundle\Entity\RetailerUserProfile;
use Symfony\Component\Validator\Constraints as Assert;

class RetailerEmployeeType extends AbstractType
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Construct
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'        => User::class,
                'validation_groups' => function (Form $form) {
                    $user = $form->getData();

                    if ($user->getId()) {
                        return ['Update', 'RetailerProfileUpdate'];
                    }
                    else {
                        return ['RetailerProfileCreate'];
                    }
                },
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Furniture\UserBundle\Entity\User $employee */
        $employee = $builder->getData();
        $builder
            ->add(
                'enabled',
                'checkbox',
                [
                    'label'    => 'frontend.enabled',
                    'required' => false,
                ]
            )
            ->add('customer', 'retailer_employee_customer', ['email_disabled' => true, 'last_name_disabled' => true,])
            ->add('retailerUserProfile', new RetailerEmployeeUserProfileType());
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'retailer_employee';
    }
}
