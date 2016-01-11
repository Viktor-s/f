<?php

namespace Furniture\FrontendBundle\Form\Type;

use Furniture\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Furniture\RetailerBundle\Entity\RetailerUserProfile;

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
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Furniture\UserBundle\Entity\User $employee */
        $employee = $builder->getData();
        /** @var \Furniture\UserBundle\Entity\User $activeUser */
        $activeUser = $this->tokenStorage->getToken()->getUser();
        $disabledMode = false;

        if ($employee && $activeUser) {
            if ($employee->getId() == $activeUser->getId()) {
                $disabledMode = true;
            }
        }

        $passwordRequired = (bool)!$employee->getId();
        
        $builder
            /*->add('username', 'text', [
                'label' => 'frontend.username',
            ])*/
            ->add('plainPassword', 'password', [
                'label' => 'frontend.password',
                'required' => $passwordRequired
            ])
            ->add('retailerMode', 'choice', [
                'property_path' => 'retailerUserProfile.retailerMode',
                'label' => 'frontend.mode',
                'disabled' => $disabledMode,
                'choices' => [
                    RetailerUserProfile::RETAILER_ADMIN => 'Admin',
                    RetailerUserProfile::RETAILER_EMPLOYEE => 'Employee',
                ]
            ])
            ->add('enabled', 'checkbox', [
                'label' => 'frontend.enabled',
                'required' => false
            ])
            ->add('customer', 'retailer_employee_customer')
            ->add('retailerUserProfile', new RetailerUserProfileType());
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'retailer_employee';
    }
}
