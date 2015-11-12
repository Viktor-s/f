<?php

namespace Furniture\FrontendBundle\Form\Type;

use Furniture\CommonBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
        /** @var \Furniture\CommonBundle\Entity\User $employee */
        $employee = $builder->getData();
        /** @var \Furniture\CommonBundle\Entity\User $activeUser */
        $activeUser = $this->tokenStorage->getToken()->getUser();
        $disabledMode = false;

        if ($employee && $activeUser) {
            if ($employee->getId() == $activeUser->getId()) {
                $disabledMode = true;
            }
        }

        $builder
            ->add('username', 'text', [
                'label' => 'frontend.username',
            ])
            ->add('plainPassword', 'password', [
                'label' => 'frontend.password'
            ])
            ->add('retailerMode', 'choice', [
                'label' => 'frontend.mode',
                'disabled' => $disabledMode,
                'choices' => [
                    User::RETAILER_ADMIN => 'Admin',
                    User::RETAILER_EMPLOYEE => 'Employee',
                ]
            ])
            ->add('customer', new RetailerEmployeeCustomerType());
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'retailer_employee';
    }
}
