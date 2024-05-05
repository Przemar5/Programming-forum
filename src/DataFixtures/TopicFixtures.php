<?php

namespace App\DataFixtures;

use App\Services\EntityHelper;
use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\TagFixtures;
use App\DataFixtures\UserFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TopicFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $topicsData = EntityHelper::getDataFromCsvFile(__DIR__ . '/topics.csv');
        for ($i = 0; $i < count($topicsData); $i++) {
            if ($topicsData[$i]['tag']) {
                $topicsData[$i]['tag'] = explode(', ', $topicsData[$i]['tag']);
            } else {
                $topicsData[$i]['tag'] = [];
            }
        }
        
        EntityHelper::convertDataToEntitiesAndSave(
            $manager, 
            $topicsData, 
            'App\\Entity\\Topic', 
            [
                'tag' => 'App\\Entity\\Tag::name', 
                'category' => 'App\\Entity\\Category::name', 
                'user' => 'App\\Entity\\User::login',
            ],
            ['setCreatedAt' => [], 'setAccepted' => [true]],
            ['tag']
        );
    }

    public function getDependencies(): array
    {
        return [CategoryFixtures::class, TagFixtures::class, UserFixtures::class];
    }
}
