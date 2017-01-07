<?php

namespace Trejjam\MailChimp\Group;

use GuzzleHttp;
use Nette;
use Trejjam;
use Trejjam\MailChimp;
use Schematic;

class Lists
{
	const GROUP_PREFIX        = '/lists';
	const GROUP_MEMBER_PREFIX = '/members';

	/**
	 * @var Trejjam\MailChimp\Request
	 */
	private $apiRequest;

	function __construct(Trejjam\MailChimp\Request $apiRequest)
	{
		$this->apiRequest = $apiRequest;
	}

	/**
	 * @return Trejjam\MailChimp\Entity\Lists\Lists|Schematic\Entry
	 * @throws Nette\Utils\JsonException
	 */
	public function getAll()
	{
		return $this->apiRequest->get(self::GROUP_PREFIX, Trejjam\MailChimp\Entity\Lists\Lists::class);
	}

	/**
	 * @param string $listId
	 *
	 * @return Trejjam\MailChimp\Entity\Lists\ListItem|Schematic\Entry
	 * @throws Nette\Utils\JsonException
	 */
	public function get($listId)
	{
		try {
			return $this->apiRequest->get($this->getEndpointPath($listId), Trejjam\MailChimp\Entity\Lists\ListItem::class);
		}
		catch (GuzzleHttp\Exception\ClientException $clientException) {
			throw new MailChimp\Exception\ListNotFoundException("List '{$listId}' not found", $clientException);
		}
	}

	/**
	 * @param string $listId
	 *
	 * @return MailChimp\Entity\Lists\Member\Lists|Schematic\Entry
	 * @throws Nette\Utils\JsonException
	 */
	public function getMembers($listId)
	{
		try {
			return $this->apiRequest->get($this->getEndpointPath($listId) . self::GROUP_MEMBER_PREFIX, MailChimp\Entity\Lists\Member\Lists::class);
		}
		catch (GuzzleHttp\Exception\ClientException $clientException) {
			throw new MailChimp\Exception\ListNotFoundException("List '{$listId}' not found", $clientException);
		}
	}

	private function getEndpointPath($listId)
	{
		return self::GROUP_PREFIX . "/{$listId}";
	}
}
