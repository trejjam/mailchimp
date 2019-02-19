<?php

namespace Trejjam\MailChimp\Group;

use GuzzleHttp;
use Nette;
use Trejjam;
use Trejjam\MailChimp;
use Schematic;

final class Lists
{
    private const GROUP_PREFIX = 'lists';
    private const GROUP_MEMBER_PREFIX = '/members';
    private const GROUP_SEGMENT_PREFIX = '/segments';

    /**
     * @var Trejjam\MailChimp\Request
     */
    private $apiRequest;

    function __construct(Trejjam\MailChimp\Request $apiRequest)
    {
        $this->apiRequest = $apiRequest;
    }

    /**
     * @throws Nette\Utils\JsonException
     */
    public function getAll() : Trejjam\MailChimp\Entity\Lists\Lists
    {
        return $this->apiRequest->get(self::GROUP_PREFIX, Trejjam\MailChimp\Entity\Lists\Lists::class);
    }

    /**
     * @throws Nette\Utils\JsonException
     * @throws MailChimp\Exception\ListNotFoundException
     */
    public function get(string $listId) : Trejjam\MailChimp\Entity\Lists\ListItem
    {
        try {
            return $this->apiRequest->get($this->getEndpointPath($listId), Trejjam\MailChimp\Entity\Lists\ListItem::class);
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            throw new MailChimp\Exception\ListNotFoundException("List '{$listId}' not found", $clientException);
        }
    }

    /**
     * @throws Nette\Utils\JsonException
     * @throws MailChimp\Exception\ListNotFoundException
     */
    public function getMembers(string $listId) : MailChimp\Entity\Lists\Member\Lists
    {
        try {
            return $this->apiRequest->get($this->getEndpointPath($listId) . self::GROUP_MEMBER_PREFIX, MailChimp\Entity\Lists\Member\Lists::class);
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            throw new MailChimp\Exception\ListNotFoundException("List '{$listId}' not found", $clientException);
        }
    }

    /**
     * @throws Nette\Utils\JsonException
     * @throws MailChimp\Exception\MemberNotFoundException
     */
    public function getMember(string $listId, string $memberHash) : MailChimp\Entity\Lists\Member\MemberItem
    {
        try {
            return $this->apiRequest->get($this->getMemberEndpointPath($listId, $memberHash), MailChimp\Entity\Lists\Member\MemberItem::class);
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            throw new MailChimp\Exception\MemberNotFoundException("Member '{$memberHash}' not found in list '{$listId}' not found", $clientException);
        }
    }

    /**
     * @throws Nette\Utils\JsonException
     * @throws MailChimp\Exception\MemberNotFoundException
     */
    public function addMember(MailChimp\Entity\Lists\Member\MemberItem $memberItem) : MailChimp\Entity\Lists\Member\MemberItem
    {
        try {
            return $this->apiRequest->put(
                $this->getMemberEndpointPath(
                    $memberItem->list_id,
                    $memberItem->id
                ),
                $memberItem->toArray(), MailChimp\Entity\Lists\Member\MemberItem::class
            );
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            throw new MailChimp\Exception\MemberNotFoundException("Member '{$memberItem->id}' not added into list '{$memberItem->list_id}'", $clientException);
        }
    }

    /**
     * @throws Nette\Utils\JsonException
     * @throws MailChimp\Exception\MemberNotFoundException
     */
    public function updateMember(MailChimp\Entity\Lists\Member\MemberItem $memberItem) : MailChimp\Entity\Lists\Member\MemberItem
    {
        try {
            return $this->apiRequest->patch(
                $this->getMemberEndpointPath(
                    $memberItem->list_id,
                    $memberItem->id
                ),
                $memberItem->getUpdated(), MailChimp\Entity\Lists\Member\MemberItem::class
            );
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            throw new MailChimp\Exception\MemberNotFoundException("Member '{$memberItem->id}' not added into list '{$memberItem->list_id}'", $clientException);
        }
    }

    /**
     * @throws Nette\Utils\JsonException
     * @throws MailChimp\Exception\MemberNotFoundException
     * @throws MailChimp\Exception\RequestException
     */
    public function removeMember(MailChimp\Entity\Lists\Member\MemberItem $memberItem) : ?array
    {
        try {
            return $this->apiRequest->delete($this->getMemberEndpointPath($memberItem->list_id, $memberItem->id));
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            throw new MailChimp\Exception\MemberNotFoundException("Member '{$memberItem->id}' not found in list '{$memberItem->list_id}' not found", $clientException);
        } catch (MailChimp\Exception\RequestException $requestException) {
            if ($requestException->getCode() === 204) {
                return null;
            }
            else {
                throw $requestException;
            }
        }
    }

    /**
     * @throws Nette\Utils\JsonException
     * @throws MailChimp\Exception\ListNotFoundException
     */
    public function getSegments(string $listId) : MailChimp\Entity\Lists\Segment\Lists
    {
        try {
            return $this->apiRequest->get($this->getEndpointPath($listId) . self::GROUP_SEGMENT_PREFIX, MailChimp\Entity\Lists\Segment\Lists::class);
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            throw new MailChimp\Exception\ListNotFoundException("List '{$listId}' not found", $clientException);
        }
    }

    /**
     * @throws Nette\Utils\JsonException
     * @throws MailChimp\Exception\ListNotFoundException
     */
    public function getSegment(string $listId, int $segmentId) : MailChimp\Entity\Lists\Segment\Segment
    {
        try {
            return $this->apiRequest->get($this->getSegmentEndpointPath($listId, $segmentId), MailChimp\Entity\Lists\Segment\Segment::class);
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            throw new MailChimp\Exception\ListNotFoundException("Segment '{$segmentId}' not found in list '{$listId}' not found", $clientException);
        }
    }

    /**
     * @throws Nette\Utils\JsonException
     * @throws MailChimp\Exception\MemberNotFoundException
     */
    public function addSegmentMember(int $segmentId, MailChimp\Entity\Lists\Member\MemberItem $memberItem) : MailChimp\Entity\Lists\Member\MemberItem
    {
        try {
            return $this->apiRequest->post(
                $this->getSegmentEndpointPath($memberItem->list_id, $segmentId) . self::GROUP_MEMBER_PREFIX,
                ['email_address' => $memberItem->email_address,
                 'status'        => 'subscribed'], MailChimp\Entity\Lists\Member\MemberItem::class
            );
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            \Tracy\Debugger::getLogger()->log($clientException);
            throw new MailChimp\Exception\MemberNotFoundException("Member '{$memberItem->id}' not added into segment '{$segmentId}'", $clientException);
        }
    }

    private function getEndpointPath(string $listId) : string
    {
        return self::GROUP_PREFIX . "/{$listId}";
    }

    private function getMemberEndpointPath(string $listId, string $memberHash) : string
    {
        return $this->getEndpointPath($listId) . self::GROUP_MEMBER_PREFIX . "/{$memberHash}";
    }

    private function getSegmentEndpointPath(string $listId, string $segmentId) : string
    {
        return $this->getEndpointPath($listId) . self::GROUP_SEGMENT_PREFIX . "/{$segmentId}";
    }
}
