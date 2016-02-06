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
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => function (Form $form) {
                $user = $form->getData();

                if ($user->getId()) {
                    return ['Update', 'RetailerProfileUpdate'];
                } else {
                    return ['Create', 'RetailerProfileCreate'];
                }
            }
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

        $passwordRequired = (bool) !$employee->getId();
        
        $builder
            ->add('plainPassword', 'password', [
                'label' => 'frontend.password',
                'required' => $passwordRequired
            ])
            ->add('enabled', 'checkbox', [
                'label' => 'frontend.enabled',
                'required' => false
            ])
            ->add('customer', 'retailer_employee_customer')
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