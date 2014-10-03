<?php

/**
 * This file is part of the Nextras\ORM library.
 *
 * @license    MIT
 * @link       https://github.com/nextras/orm
 * @author     Jan Skrasek
 */

namespace Nextras\Orm\StorageReflection;

use Nette\Object;
use Nextras\Orm\Mapper\IMapper;


class CommonReflection extends Object implements IStorageReflection
{
	/** @var IMapper */
	private $mapper;

	/** @var string */
	private $storageName;


	public function __construct(IMapper $mapper, $storageName)
	{
		$this->mapper = $mapper;
		$this->storageName = $storageName;
	}


	public function getStorageName()
	{
		return $this->storageName;
	}


	public function getStoragePrimaryKey()
	{
		return ['id'];
	}


	public function convertEntityToStorage($data)
	{
		$data = (array) $data;
		return $data;
	}


	public function convertStorageToEntity($data)
	{
		$data = (array) $data;
		return $data;
	}


	public function convertEntityToStorageKey($key)
	{
		return $key;
	}


	public function convertStorageToEntityKey($key)
	{
		return $key;
	}


	private function renameKey(& $array, $oldKey, $newKey)
	{
		if ($oldKey !== $newKey && array_key_exists($oldKey, $array)) {
			$array[$newKey] = $array[$oldKey];
			unset($array[$oldKey]);
		}
	}

}
