<?php

namespace Trejjam\MailChimp\Entity;

use Trejjam;

abstract class AEntity
{
	/**
	 * @var boolean[]
	 */
	protected $readOnly = [];

	protected $associations = [];

	protected $initData = [];

	protected $data = [];

	public function __construct(array $data)
	{
		foreach ($this->associations as $key => $class) {
			if ( !array_key_exists($key, $data)) {
				continue;
			}

			if (is_array($class)) {
				$class = $class[0];

				$data[$key] = new Entries($data[$key], $class);
			}
			else {
				$data[$key] = new $class($data[$key]);
			}
		}

		$this->initData = $data;
		$this->data = $data;

		$this->init();
	}

	public function init()
	{

	}

	public function __set($key, $value)
	{
		if (array_key_exists($key, $this->readOnly)) {
			throw new Trejjam\MailChimp\Exception\ReadOnlyEntityException($key);
		}

		$this->data[$key] = $value;
	}

	public function __get($key)
	{
		if ( !array_key_exists($key, $this->data)) {
			return NULL;
		}

		return $this->data[$key];
	}

	public function toArray()
	{
		$out = $this->data;

		foreach ($out as $key => $entityData) {
			if ($entityData instanceof AEntity) {
				$out[$key] = $entityData->toArray();
			}
			else if ($entityData instanceof Entries) {
				$out[$key] = $entityData->toArray();
			}
		}

		return $out;
	}
}
