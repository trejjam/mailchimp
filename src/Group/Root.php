<?php

namespace Trejjam\MailChimp\Group;

use Nette;
use Trejjam;

class Root
{
	/**
	 * @var Trejjam\MailChimp\Request
	 */
	private $apiRequest;

	function __construct(Trejjam\MailChimp\Request $apiRequest)
	{
		$this->apiRequest = $apiRequest;
	}

	/**
	 * @return Trejjam\MailChimp\Entity\Root
	 * @throws Nette\Utils\JsonException
	 */
	public function get()
	{
		return $this->apiRequest->get('/', Trejjam\MailChimp\Entity\Root::class);
	}
}
