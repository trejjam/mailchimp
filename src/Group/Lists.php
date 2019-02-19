<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Group;

use GuzzleHttp;
use Nette\Utils\JsonException;
use Trejjam\MailChimp\Request;
use Trejjam\MailChimp\Entity\Lists\Member\MemberItem;
use Trejjam\MailChimp\Entity\Lists\ListItem;
use Trejjam\MailChimp\Entity\Lists\Lists as EntityLists;
use Trejjam\MailChimp\Entity\Lists\Member\Lists as EntityMemberLists;
use Trejjam\MailChimp\Entity\Lists\Segment\Segment;
use Trejjam\MailChimp\Entity\Lists\Segment\Lists as EntitySegmentLists;
use Trejjam\MailChimp\Exception\ListNotFoundException;
use Trejjam\MailChimp\Exception\MemberNotFoundException;
use Trejjam\MailChimp\Exception\RequestException;
use Schematic;

final class Lists
{
    private const GROUP_PREFIX = 'lists';
    private const GROUP_MEMBER_PREFIX = '/members';
    private const GROUP_SEGMENT_PREFIX = '/segments';

    /**
     * @var Request
     */
    private $apiRequest;

    function __construct(Request $apiRequest)
    {
        $this->apiRequest = $apiRequest;
    }

    /**
     * @throws JsonException
     */
    public function getAll() : EntityLists
    {
        return $this->apiRequest->get($this->getEndpointPath(), EntityLists::class);
    }

    /**
     * @throws JsonException
     * @throws ListNotFoundException
     */
    public function get(string $listId) : ListItem
    {
        try {
            return $this->apiRequest->get($this->getListEndpointPath($listId), ListItem::class);
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            throw new ListNotFoundException("List '{$listId}' not found", $clientException);
        }
    }

    /**
     * @throws JsonException
     * @throws ListNotFoundException
     */
    public function getMembers(string $listId) : EntityMemberLists
    {
        try {
            return $this->apiRequest->get($this->getMemberEndpointPath($listId), EntityMemberLists::class);
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            throw new ListNotFoundException("List '{$listId}' not found", $clientException);
        }
    }

    /**
     * @throws JsonException
     * @throws MemberNotFoundException
     */
    public function getMember(string $listId, string $memberHash) : MemberItem
    {
        try {
            return $this->apiRequest->get($this->getOneMemberEndpointPath($listId, $memberHash), MemberItem::class);
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            throw new MemberNotFoundException("Member '{$memberHash}' not found in the list '{$listId}'", $clientException);
        }
    }

    /**
     * @throws JsonException
     * @throws MemberNotFoundException
     */
    public function addMember(MemberItem $memberItem) : MemberItem
    {
        try {
            return $this->apiRequest->put(
                $this->getOneMemberEndpointPath(
                    $memberItem->list_id,
                    $memberItem->id
                ),
                $memberItem->toArray(), MemberItem::class
            );
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            throw new MemberNotFoundException("Member '{$memberItem->id}' not added into list '{$memberItem->list_id}'", $clientException);
        }
    }

    /**
     * @throws JsonException
     * @throws MemberNotFoundException
     */
    public function updateMember(MemberItem $memberItem) : MemberItem
    {
        try {
            return $this->apiRequest->patch(
                $this->getOneMemberEndpointPath(
                    $memberItem->list_id,
                    $memberItem->id
                ),
                $memberItem->getUpdated(), MemberItem::class
            );
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            throw new MemberNotFoundException("Member '{$memberItem->id}' not added into list '{$memberItem->list_id}'", $clientException);
        }
    }

    /**
     * @throws JsonException
     * @throws MemberNotFoundException
     * @throws RequestException
     */
    public function removeMember(MemberItem $memberItem) : ?array
    {
        try {
            return $this->apiRequest->delete($this->getOneMemberEndpointPath($memberItem->list_id, $memberItem->id));
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            throw new MemberNotFoundException("Member '{$memberItem->id}' not found in the list '{$memberItem->list_id}'", $clientException);
        } catch (RequestException $requestException) {
            if ($requestException->getCode() === 204) {
                return null;
            }
            else {
                throw $requestException;
            }
        }
    }

    /**
     * @throws JsonException
     * @throws ListNotFoundException
     */
    public function getSegments(string $listId) : EntitySegmentLists
    {
        try {
            return $this->apiRequest->get($this->getSegmentEndpointPath($listId), EntitySegmentLists::class);
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            throw new ListNotFoundException("List '{$listId}' not found", $clientException);
        }
    }

    /**
     * @throws JsonException
     * @throws ListNotFoundException
     */
    public function getSegment(string $listId, int $segmentId) : Segment
    {
        try {
            return $this->apiRequest->get($this->getOneSegmentEndpointPath($listId, $segmentId), Segment::class);
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            throw new ListNotFoundException("Segment '{$segmentId}' not found in the list '{$listId}'", $clientException);
        }
    }

    /**
     * @throws JsonException
     * @throws MemberNotFoundException
     */
    public function addSegmentMember(int $segmentId, MemberItem $memberItem) : MemberItem
    {
        try {
            return $this->apiRequest->post(
                $this->getOneSegmentEndpointPath($memberItem->list_id, $segmentId) . self::GROUP_MEMBER_PREFIX,
                [
                    'email_address' => $memberItem->email_address,
                    'status'        => 'subscribed',
                ], MemberItem::class
            );
        } catch (GuzzleHttp\Exception\ClientException $clientException) {
            \Tracy\Debugger::getLogger()->log($clientException);
            throw new MemberNotFoundException("Member '{$memberItem->id}' not added into segment '{$segmentId}'", $clientException);
        }
    }

    private function getEndpointPath() : string
    {
        return self::GROUP_PREFIX;
    }

    private function getListEndpointPath(string $listId) : string
    {
        return $this->getEndpointPath() . "/{$listId}";
    }

    private function getMemberEndpointPath(string $listId) : string
    {
        return $this->getListEndpointPath($listId) . self::GROUP_MEMBER_PREFIX;
    }

    private function getOneMemberEndpointPath(string $listId, string $memberHash) : string
    {
        return $this->getMemberEndpointPath($listId) . "/{$memberHash}";
    }

    private function getSegmentEndpointPath(string $listId) : string
    {
        return $this->getListEndpointPath($listId) . self::GROUP_SEGMENT_PREFIX;
    }

    private function getOneSegmentEndpointPath(string $listId, int $segmentId) : string
    {
        return $this->getSegmentEndpointPath($listId) . "/{$segmentId}";
    }
}
