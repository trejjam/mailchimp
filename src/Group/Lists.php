<?php

namespace Trejjam\MailChimp\Group;

use GuzzleHttp;
use Nette;
use Trejjam;
use Trejjam\MailChimp;
use Schematic;

class Lists
{
	const GROUP_PREFIX        = 'lists';
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
	 * @throws MailChimp\Exception\ListNotFoundException
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
	 * @throws MailChimp\Exception\ListNotFoundException
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

	/**
	 * @param string $listId
	 * @param string $memberHash
	 *
	 * @return MailChimp\Entity\Lists\Member\MemberItem
	 * @throws Nette\Utils\JsonException
	 * @throws MailChimp\Exception\MemberNotFoundException
	 */
	public function getMember($listId, $memberHash)
	{
		try {
			return $this->apiRequest->get($this->getMemberEndpointPath($listId, $memberHash), MailChimp\Entity\Lists\Member\MemberItem::class);
		}
		catch (GuzzleHttp\Exception\ClientException $clientException) {
			throw new MailChimp\Exception\MemberNotFoundException("Member '{$memberHash}' not found in list '{$listId}' not found", $clientException);
		}
	}

	public function addMember(MailChimp\Entity\Lists\Member\MemberItem $memberItem)
	{
		try {
			return $this->apiRequest->put(
				$this->getMemberEndpointPath(
					$memberItem->list_id,
					$memberItem->id
				),
				$memberItem->toArray(), MailChimp\Entity\Lists\Member\MemberItem::class
			);
		}
		catch (GuzzleHttp\Exception\ClientException $clientException) {
			throw new MailChimp\Exception\MemberNotFoundException("Member '{$memberItem->id}' not added into list '{$memberItem->list_id}'", $clientException);
		}
	}

	public function removeMember(MailChimp\Entity\Lists\Member\MemberItem $memberItem)
	{
		try {
			return $this->apiRequest->delete($this->getMemberEndpointPath($memberItem->list_id, $memberItem->id));
		}
		catch (GuzzleHttp\Exception\ClientException $clientException) {
			throw new MailChimp\Exception\MemberNotFoundException("Member '{$memberItem->id}' not found in list '{$memberItem->list_id}' not found", $clientException);
		}
		catch (MailChimp\Exception\RequestException $requestException) {
			if ($requestException->getCode() === 204) {
				return TRUE;
			}
			else {
				throw $requestException;
			}
		}
	}

	private function getEndpointPath($listId)
	{
		return self::GROUP_PREFIX . "/{$listId}";
	}

	private function getMemberEndpointPath($listId, $memberHash)
	{
		return $this->getEndpointPath($listId) . self::GROUP_MEMBER_PREFIX . "/{$memberHash}";
	}
}
