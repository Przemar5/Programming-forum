<?php

namespace App\DataFixtures;

use App\Services\EntityHelper;
use App\DataFixtures\TopicFixtures;
use App\DataFixtures\UserFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $postsData = EntityHelper::getDataFromCsvFile(__DIR__ . '/posts.csv');
        EntityHelper::convertDataToEntitiesAndSave(
            $manager, 
            $postsData, 
            'App\\Entity\\Post', 
            [
                'topic' => 'App\\Entity\\Topic::name', 
                'user' => 'App\\Entity\\User::login',
            ],
            ['setCreatedAt' => [], 'setAccepted' => [true]]
        );
    }

    public function getDependencies(): array
    {
        return [TopicFixtures::class, UserFixtures::class];
    }
}
