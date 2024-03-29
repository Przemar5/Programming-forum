<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Services\EntityHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $users = EntityHelper::getEntitiesFromCsvFile(User::class, __DIR__ . '/users.csv');

        foreach ($users as $user) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, bin2hex(random_bytes(20))));
            $user->setAvatar(User::DEFAULT_AVATAR);
            $user->setCreatedAt();
            $manager->persist($user);
        }

        $manager->flush();
    }
}
