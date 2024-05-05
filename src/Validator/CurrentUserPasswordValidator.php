<?php

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CurrentUserPasswordValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\CurrentUserPassword $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        if (password_verify($value, $user->getPassword())) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
