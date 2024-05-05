<?php

namespace App\Services;

use Doctrine\Persistence\ObjectManager;

class EntityHelper
{
	public static function getEntitiesFromCsvFile(string $entityClass, string $filePath): array
    {
    	return self::convertDataIntoEntities(self::getDataFromCsvFile($filePath), $entityClass);
    }

    public static function convertDataIntoEntities(array $data, string $entityClass): array
    {
    	return array_map(fn($arr) => self::convertDataIntoEntity($arr, $entityClass), $data);
    }

    public static function convertDataIntoEntity(array $attributeValueMap, string $entityClass, array $multiple = []): object
    {
    	$entity = new $entityClass();
		foreach ($attributeValueMap as $attribute => $value) {
            if (in_array($attribute, $multiple)) {
                foreach ($value as $v) {
                    $entity->{'add'.ucfirst($attribute)}($v);
                }
            } else {
                $entity->{'set'.ucfirst($attribute)}($value);
            }
        }

        return $entity;
    }

	public static function getDataFromCsvFile(string $filePath): array
    {
        $fileContents = file_get_contents($filePath);
        $lines = explode("\n", $fileContents);
        $attributes = explode("\t", array_shift($lines));
        $linesCount = count($lines);
        $attributesCount = count($attributes);
        $result = [];

        return array_map(function ($line) use ($attributes) {
        	return array_combine($attributes, explode("\t", $line));
        }, $lines);
    }

    /**
     * @param array $attributeToEntityAttributeMap ['attribute' => 'EntityClass::entityAttribute', ...]
     * @param array $methodsToCallBeforePersist ['methodName' => ['firstArg', 'secondArg', ...], ...]
     * @param array $attributesWithMultipleValues ['firstAttribute', 'secondAttribute', ...]
     */
    public static function convertDataToEntitiesAndSave(
		ObjectManager $manager,
    	array $entitiesData, 
    	string $entityClass, 
    	array $attributeToEntityAttributeMap = [],
    	array $methodsToCallBeforePersist = [],
        array $attributesWithMultipleValues = []
    ) {
    	$parentEntityAttr = null;

    	foreach ($attributeToEntityAttributeMap as $attribute => $entityAttribute) {
           	[$otherEntityClass, $otherEntityAttribute] = explode('::', $entityAttribute);
           	if ($otherEntityClass === $entityClass) {
           		$parentEntityAttr = [$attribute, $otherEntityAttribute];
           	}
        }
        
        if ($parentEntityAttr) {
	    	$flushedEntityAttrs = [];
	        $unflushedEntityAttrs = [];
	    }

        foreach ($entitiesData as $entityData) {
            foreach ($attributeToEntityAttributeMap as $attribute => $entityAttribute) {
            	[$otherEntityClass, $otherEntityAttribute] = explode('::', $entityAttribute);

				if ($entityData[$attribute] === '') {
		            $entityData[$attribute] = null;

		        } else {
	                if ($otherEntityClass === $entityClass) {
		                if (in_array($entityData[$attribute], $unflushedEntityAttrs)) {
		                    $manager->flush();
		                    $flushedEntityAttrs = array_merge($flushedEntityAttrs, $unflushedEntityAttrs);
		                    $unflushedEntityAttrs = [];
		                }
		                if (!in_array($entityData[$attribute], $flushedEntityAttrs)) {
		                    throw new \Exception("Missing parent entity with $parentEntityReferencedAttr '" . $entityData[$attribute] . "'.");
		                }
	            	}

                    if (is_array($entityData[$attribute])) {
                    	$entityData[$attribute] = $manager->getRepository($otherEntityClass)->findBy([
                    		$otherEntityAttribute => $entityData[$attribute],
                    	]);
                    } else {
                        $entityData[$attribute] = $manager->getRepository($otherEntityClass)->findOneBy([
                            $otherEntityAttribute => $entityData[$attribute],
                        ]);
                    }
	            }
            }

            $entity = EntityHelper::convertDataIntoEntity($entityData, $entityClass, $attributesWithMultipleValues);
            foreach ($methodsToCallBeforePersist as $method => $args) {
	            $entity->{$method}(...$args);
	        }
            $manager->persist($entity);
            if ($parentEntityAttr) {
	            $unflushedEntityAttrs[] = $entityData[$parentEntityAttr[1]];
	        }
        }

        $manager->flush();
    }
}