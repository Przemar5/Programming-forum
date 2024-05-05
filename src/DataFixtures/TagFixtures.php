<?php

namespace App\DataFixtures;

use App\Services\EntityHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tags = EntityHelper::getEntitiesFromCsvFile('App\\Entity\\Tag', __DIR__ . '/tags.csv');

        foreach ($tags as $tag) {
            $tag->setCreatedAt();
            $manager->persist($tag);
        }

        $manager->flush();
    }
}
