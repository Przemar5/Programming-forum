<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\CurrentUserPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CurrentUserPasswordType extends PasswordType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => true,
            'constraints' => [
                new CurrentUserPassword('Password is invalid.'),
            ],
        ]);
    }
}
