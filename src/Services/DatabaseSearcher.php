<?php

namespace App\Services;

class DatabaseSearcher
{
	private $database;
	private array $resources;
	private string $phrase;
	private array $results = [];

	public function __construct($database)
	{
		$this->database = $database;
	}

	public function search(string $phrase, array $resources)
	{
		$this->phrase = $phrase;
		$this->resources = $resources;

		$words = explode(' ', $this->phrase);

		foreach ($this->resources as [$entity, $name, $fields, $softDelete]) {

            $this->results[$name] = $this->database
                ->getManager()
                ->getRepository($entity)
                ->createQueryBuilder('o')
            ;

            foreach ($fields as $field) {

            	foreach ($words as $word) {
	                $this->results[$name] = $this->results[$name]
	                    ->andWhere('o.'.$field.' LIKE :'.$field)
	                    ->setParameter($field, '%'.$word.'%')
	                ;
	            }
            }

            if ($softDelete) {
	            $this->results[$name] = $this->results[$name]
	            	->andWhere('o.deleted_at IS NULL')
	            ;
            }

            $this->results[$name] = $this->results[$name]
                ->getQuery()
                ->execute()
            ;
        }

        return $this;
	}

	public function getResults()
	{
		return $this->results;
	}
}