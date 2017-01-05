<?php

namespace Trejjam\MailChimp;

use Nette;
use Trejjam;

class Context
{
	/**
	 * @var Group\Root
	 */
	private $root;
	/**
	 * @var Group\Lists
	 */
	private $lists;

	public function __construct(
		Trejjam\MailChimp\Group\Root $root,
		Trejjam\MailChimp\Group\Lists $lists
	) {
		$this->root = $root;
		$this->lists = $lists;
	}

	public function getRootGroup()
	{
		return $this->root;
	}

	public function getListsGroup()
	{
		return $this->lists;
	}
}
