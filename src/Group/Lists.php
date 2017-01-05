<?php

namespace Trejjam\MailChimp\Group;

use Nette;
use Trejjam;

class Lists
{
	const GROUP_PREFIX = '/lists';

	/**
	 * @var Trejjam\MailChimp\Request
	 */
	private $apiRequest;

	function __construct(Trejjam\MailChimp\Request $apiRequest)
	{
		$this->apiRequest = $apiRequest;
	}

	/**
	 * @return Trejjam\MailChimp\Entity\Lists\Lists
	 * @throws Nette\Utils\JsonException
	 */
	public function get()
	{
		return $this->apiRequest->get(self::GROUP_PREFIX, Trejjam\MailChimp\Entity\Lists\Lists::class);
	}
}
