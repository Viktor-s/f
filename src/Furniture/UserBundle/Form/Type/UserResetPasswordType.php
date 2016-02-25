<?php

namespace Furniture\UserBundle\Form\Type;

use Furniture\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class UserResetPasswordType extends AbstractType
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    public function __construct(TokenStorageInterface $tokenStorage, EncoderFactoryInterface $encoder)
    {
        $this->tokenStorage = $tokenStorage;
        $this->encoderFactory = $encoder;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'password',
                'repeated',
                [
                    'label'           => 'New password',
                    'type'            => 'password',
                    'invalid_message' => 'The password fields must match.',
                    'first_options'   => [
                        'label'       => 'Password',
                        'attr'        => [
                            'maxlength' => 128,
                        ],
                        'constraints' => [
                            new Assert\NotBlank(),
                            new Assert\Length(
                                [
                                    'min' => 4,
                                    'max' => 128,
                                ]
                            ),
                        ],
                    ],
                    'second_options'  => [
                        'label'       => 'Please confirm password',
                        'constraints' => [
                            new Assert\NotBlank(),
                            new Assert\Length(
                                [
                                    'min' => 4,
                                    'max' => 128,
                                ]
                            ),
                        ],
                        'attr'            => [
                            'maxlength'   => 128,
                        ],
                    ],
                ]
            )
            ->add(
                'submit',
                'submit',
                [
                    'label' => 'Change password',
                ]
            );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                if (!isset($data['user']) || !$data['user'] instanceof User) {
                    return;
                }

                /** @var  User $user */
                $user = $data['user'];

                if ($user->isDisabled()) {
                    $form->addError(new FormError('Your account has been disabled. Please contact support!.'));
                }

                if (!isset($data['password'])) {
                    return;
                }

                $encoder = $this->encoderFactory->getEncoder($user);

                if ($encoder->isPasswordValid($user->getPassword(), $data['password'], $user->getSalt())) {
                    $form->addError(new FormError('This value shouldn\'t be the user\'s current password.'));
                }
            }
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'reset_password';
    }
}
