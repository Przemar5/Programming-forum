<?php

namespace App\Form;

use App\Entity\User;
use App\Form\CurrentUserPasswordType;
use App\Form\RepeatedPasswordType;
use App\Validator\CurrentUserPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', CurrentUserPasswordType::class)
            ->add('password', RepeatedPasswordType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'data_class' => User::class,
        ]);
    }
}
