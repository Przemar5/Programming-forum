<?php

namespace App\DataFixtures;

use App\Services\EntityHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categoriesData = EntityHelper::getDataFromCsvFile(__DIR__ . '/categories.csv');
        EntityHelper::convertDataToEntitiesAndSave(
            $manager, 
            $categoriesData, 
            'App\\Entity\\Category', 
            ['parentCategory' => 'App\\Entity\\Category::name'],
            ['setCreatedAt' => []]
        );
    }
}
