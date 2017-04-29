<?php

namespace Trejjam\MailChimp;

class Lists
{
	/**
	 * @var string[]
	 */
	private $lists;

	public function __construct(array $lists)
	{
		$this->lists = $lists;
	}

	public function getListByName($name)
	{
		return $this->lists[$name];
	}
}
