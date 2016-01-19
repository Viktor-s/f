<?php

namespace Furniture\FrontendBundle\Form\Type;

use Furniture\UserBundle\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class RetailerEmployeeCustomerType extends AbstractType
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', [
                'label' => 'frontend.email',
            ])
            ->add('firstName', 'text', [
                'label' => 'frontend.first_name',
            ])
            ->add('lastName', 'text', [
                'label'    => 'frontend.last_name',
                'required' => false,
            ]);

        $em = $this->em;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($em) {
            $event->stopPropagation();

            /** @var Customer $customer */
            $customer = $event->getData();

            if ($customer->getId()) {
                return;
            }

            /** @var \Sylius\Bundle\UserBundle\Doctrine\ORM\CustomerRepository $cusomerRepositroy */
            $cusomerRepositroy = $em->getRepository(Customer::class);
            $em->getFilters()->disable('softdeleteable');

            $form = $event->getForm();
            $email = $customer->getEmail();

            if ($cusomerRepositroy->findOneByEmail($email)) {
                $form->get('email')->addError(new FormError('Already in use!'));
            }
        }, 900);

    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'retailer_employee_customer';
    }
}
