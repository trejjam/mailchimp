<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Tests\Group;

use Nette\DI\Container;
use Nette\Utils\Random;
use Tester\Assert;
use Tester\TestCase;
use Trejjam\MailChimp;
use Trejjam\MailChimp\Exception\MemberNotFoundException;
use Trejjam\MailChimp\Exception\ListNotFoundException;

$container = require __DIR__ . '/../../bootstrap.php';

final class ListsTest extends TestCase
{
    private const TEST_LIST = 'testList';

    /**
     * @var Container
     */
    private $container;

    function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function testGetAll() : void
    {
        $groupLists = $this->container->getByType(MailChimp\Group\Lists::class);

        Assert::type(MailChimp\Group\Lists::class, $groupLists);

        $listsEntity = $groupLists->getAll();
        Assert::type(MailChimp\Entity\Lists\Lists::class, $listsEntity);

        $listItems = $listsEntity->getLists();

        if (count($listItems) > 0) {
            $listItem = $listItems[0];

            Assert::type(MailChimp\Entity\Lists\ListItem::class, $listItem);
            Assert::notSame(null, $listItem->id);
            Assert::type(MailChimp\Entity\Contact::class, $listItem->getContact());
            Assert::type(MailChimp\Entity\Link::class, $listItem->getLinks()[0]);
        }
    }

    public function testGetEntity() : void
    {
        $groupLists = $this->container->getByType(MailChimp\Group\Lists::class);

        Assert::type(MailChimp\Group\Lists::class, $groupLists);

        Assert::throws(function () use ($groupLists) {
            $groupLists->get('not_exist_id');
        }, ListNotFoundException::class);

        $listsEntity = $groupLists->getAll();
        if (count($listsEntity->getLists()) > 0) {
            $_listEntity = $listsEntity->getLists()[0];

            $listEntity = $groupLists->get($_listEntity->id);
            Assert::type(MailChimp\Entity\Lists\ListItem::class, $listEntity);
            Assert::notSame(null, $listEntity->id);
            Assert::type(MailChimp\Entity\Contact::class, $listEntity->getContact());
            Assert::type(MailChimp\Entity\Link::class, $listEntity->getLinks()[0]);
        }
    }

    public function testGetEntityMembers() : void
    {
        $groupLists = $this->container->getByType(MailChimp\Group\Lists::class);

        Assert::throws(function () use ($groupLists) {
            $groupLists->getMembers('not_exist_id');
        }, ListNotFoundException::class);

        $listsEntity = $groupLists->getAll();
        if (count($listsEntity->getLists()) > 0) {
            $_listEntity = $listsEntity->getLists()[0];

            $listMembers = $groupLists->getMembers($_listEntity->id);
            Assert::type(MailChimp\Entity\Lists\Member\Lists::class, $listMembers);
            Assert::type(MailChimp\Entity\Link::class, $listMembers->getLinks()[0]);
            Assert::same($_listEntity->id, $listMembers->list_id);

            $listMemberItems = $listMembers->getMembers();

            if (count($listMemberItems) > 0) {
                $listMemberItem = $listMemberItems[0];
                Assert::type(MailChimp\Entity\Lists\Member\MemberItem::class, $listMemberItem);
                Assert::type(MailChimp\Entity\Link::class, $listMemberItem->getLinks()[0]);
                Assert::same($_listEntity->id, $listMemberItem->list_id);

                $memberItem = MailChimp\Entity\Lists\Member\MemberItem::create($listMemberItem->email_address, $listMemberItem->list_id);
                Assert::same($listMemberItem->id, $memberItem->id);
                Assert::same($listMemberItem->list_id, $memberItem->list_id);
            }

            Assert::throws(function () {
                MailChimp\Entity\Lists\Member\MemberItem::create('not_email_address', 'not_important_list_id');
            }, MailChimp\Exception\CoruptedEmailException::class);
        }
    }

    public function testGetEntityMember() : void
    {
        $groupLists = $this->container->getByType(MailChimp\Group\Lists::class);
        $lists = $this->container->getByType(MailChimp\Lists::class);

        $testList = $lists->getListByName(self::TEST_LIST);

        $memberItem3 = MailChimp\Entity\Lists\Member\MemberItem::create(
            $this->getUniqueDummyMail(1),
            $testList,
            MailChimp\Entity\Lists\Member\MemberItem::STATUS_SUBSCRIBED
        );
        $memberItem3->setMergeFields(
            [
                'FNAME' => 'Jan',
                'LNAME' => 'Trejbal',
            ]
        );
        $groupLists->addMember($memberItem3);

        $listMembers = $groupLists->getMembers($testList);
        $listMemberItems = $listMembers->getMembers();

        if (count($listMemberItems) > 0) {
            $_listMemberItem = $listMemberItems[0];

            Assert::throws(function () use ($groupLists, $_listMemberItem) {
                $groupLists->getMember($_listMemberItem->list_id, 'not_exist_id');
            }, MemberNotFoundException::class);

            Assert::throws(function () use ($groupLists, $_listMemberItem) {
                $groupLists->getMember('not_exist_id', $_listMemberItem->id);
            }, MemberNotFoundException::class);

            $listMemberItem = $groupLists->getMember($_listMemberItem->list_id, $_listMemberItem->id);

            Assert::type(MailChimp\Entity\Lists\Member\MemberItem::class, $listMemberItem);
            Assert::type(MailChimp\Entity\Link::class, $listMemberItem->getLinks()[0]);
        }

        $groupLists->removeMember($memberItem3);
    }

    public function testAddUpdateDeleteEntityMember() : void
    {
        $groupLists = $this->container->getByType(MailChimp\Group\Lists::class);
        $lists = $this->container->getByType(MailChimp\Lists::class);

        $testList = $lists->getListByName(self::TEST_LIST);

        $memberEmail1 = $this->getUniqueDummyMail(2);
        $newMemberEmail1 = $this->getUniqueDummyMail(3);

        $memberItem1 = MailChimp\Entity\Lists\Member\MemberItem::create(
            $memberEmail1,
            $testList,
            MailChimp\Entity\Lists\Member\MemberItem::STATUS_SUBSCRIBED
        );
        $memberItem1->setMergeFields(
            [
                'FNAME' => 'Jan',
                'LNAME' => 'Trejbal',
            ]
        );
        $memberItemAdd1 = $groupLists->addMember($memberItem1);

        Assert::same($memberItem1->email_address, $memberItemAdd1->email_address);
        Assert::same($memberItem1->merge_fields[MailChimp\Entity\Lists\Member\MemberItem::MERGE_FIELDS_FNAME], $memberItemAdd1->merge_fields[MailChimp\Entity\Lists\Member\MemberItem::MERGE_FIELDS_FNAME]);
        Assert::same($memberItem1->merge_fields[MailChimp\Entity\Lists\Member\MemberItem::MERGE_FIELDS_LNAME], $memberItemAdd1->merge_fields[MailChimp\Entity\Lists\Member\MemberItem::MERGE_FIELDS_LNAME]);

        $memberItemAdd1->setEmailAddress($newMemberEmail1);
        $updatedMemberItemAdd1 = $groupLists->updateMember($memberItemAdd1);

        Assert::same($newMemberEmail1, $updatedMemberItemAdd1->email_address);

        $updatedMemberItemAdd1->setEmailAddress($memberEmail1);
        $_updatedMemberItemAdd1 = $groupLists->updateMember($updatedMemberItemAdd1);

        Assert::same($memberEmail1, $_updatedMemberItemAdd1->email_address);

        $memberItem2 = MailChimp\Entity\Lists\Member\MemberItem::create(
            $this->getUniqueDummyMail(4),
            $testList,
            MailChimp\Entity\Lists\Member\MemberItem::STATUS_UNSUBSCRIBED
        );
        $memberItemAdd2 = $groupLists->addMember($memberItem2);
        Assert::same($memberItem2->email_address, $memberItemAdd2->email_address);
        Assert::notSame($memberItem1->email_address, $memberItemAdd2->email_address);

        $memberItemGet1 = $groupLists->getMember($testList, $memberItem1->id);
        Assert::same($memberItem1->email_address, $memberItemGet1->email_address);

        $memberItemGet2 = $groupLists->getMember($testList, $memberItem2->id);
        Assert::same($memberItem2->email_address, $memberItemGet2->email_address);

        $groupLists->removeMember($memberItemGet1);
        $groupLists->getMember($testList, $memberItem2->id);
        Assert::throws(function () use ($groupLists, $memberItem1) {
            $groupLists->getMember($memberItem1->list_id, $memberItem1->id);
        }, MemberNotFoundException::class, "Member '{$memberItem1->id}' not found in the list '{$memberItem1->list_id}'");

        $groupLists->removeMember($memberItemGet2);
        Assert::throws(function () use ($groupLists, $memberItem1) {
            $groupLists->getMember($memberItem1->list_id, $memberItem1->id);
        }, MemberNotFoundException::class, "Member '{$memberItem1->id}' not found in the list '{$memberItem1->list_id}'");

        Assert::throws(function () use ($groupLists, $memberItem2) {
            $groupLists->getMember($memberItem2->list_id, $memberItem2->id);
        }, MemberNotFoundException::class, "Member '{$memberItem2->id}' not found in the list '{$memberItem2->list_id}'");
    }

    public function testGetEntitySegments() : void
    {
        $groupLists = $this->container->getByType(MailChimp\Group\Lists::class);

        Assert::throws(function () use ($groupLists) {
            $groupLists->getSegments('not_exist_id');
        }, ListNotFoundException::class);

        $listsEntity = $groupLists->getAll();
        if (count($listsEntity->getLists()) > 0) {
            $_listEntity = $listsEntity->getLists()[0];

            $listSegments = $groupLists->getSegments($_listEntity->id);
            Assert::type(MailChimp\Entity\Lists\Segment\Lists::class, $listSegments);
            Assert::type(MailChimp\Entity\Link::class, $listSegments->getLinks()[0]);
            Assert::same($_listEntity->id, $listSegments->list_id);

            $listSegmentItems = $listSegments->getSegments();

            if (count($listSegmentItems) > 0) {
                $listSegmentItem = $listSegmentItems[0];

                Assert::type(MailChimp\Entity\Lists\Segment\Segment::class, $listSegmentItem);
                Assert::type(MailChimp\Entity\Link::class, $listSegmentItem->getLinks()[0]);
                Assert::same($_listEntity->id, $listSegmentItem->list_id);

                $segment = $groupLists->getSegment($_listEntity->id, $listSegmentItem->id);

                Assert::same($listSegmentItem->id, $segment->id);
            }
        }
    }

    public function testAddSegment() : void
    {
        $groupLists = $this->container->getByType(MailChimp\Group\Lists::class);
        $lists = $this->container->getByType(MailChimp\Lists::class);

        $testList = $lists->getListByName(self::TEST_LIST);

        $listSegments = $groupLists->getSegments($testList);
        Assert::type(MailChimp\Entity\Lists\Segment\Lists::class, $listSegments);
        Assert::type(MailChimp\Entity\Link::class, $listSegments->getLinks()[0]);
        Assert::same($testList, $listSegments->list_id);

        $segmentName = 'Test segment - ' . Random::generate(10);

        $segment = $groupLists->addSegment($testList, $segmentName);
        Assert::type(MailChimp\Entity\Lists\Segment\Segment::class, $segment);
        Assert::same($segmentName, $segment->name);

        $segmentFetch = $groupLists->getSegment($testList, $segment->id);
        Assert::type(MailChimp\Entity\Lists\Segment\Segment::class, $segmentFetch);
        Assert::same($segmentName, $segmentFetch->name);
        Assert::same($segment->type, $segmentFetch->type);
        Assert::same($segment->created_at, $segmentFetch->created_at);
    }

    public function testAddSegmentMember() : void
    {
        $groupLists = $this->container->getByType(MailChimp\Group\Lists::class);
        $lists = $this->container->getByType(MailChimp\Lists::class);

        $testList = $lists->getListByName(self::TEST_LIST);

        $listSegments = $groupLists->getSegments($testList);
        Assert::type(MailChimp\Entity\Lists\Segment\Lists::class, $listSegments);
        Assert::type(MailChimp\Entity\Link::class, $listSegments->getLinks()[0]);
        Assert::same($testList, $listSegments->list_id);

        $listSegmentItems = $listSegments->getSegments();

        if (count($listSegmentItems) > 0) {
            $listSegmentItem = $listSegmentItems[0];

            $memberItem = MailChimp\Entity\Lists\Member\MemberItem::create(
                $this->getUniqueDummyMail(5),
                $testList,
                MailChimp\Entity\Lists\Member\MemberItem::STATUS_SUBSCRIBED
            );
            $memberItem->setMergeFields(
                [
                    'FNAME' => 'Jan',
                    'LNAME' => 'Trejbal',
                ]
            );
            $groupLists->addMember($memberItem);

            $segmentMemberItem = $groupLists->addSegmentMember($listSegmentItem->id, $memberItem);

            Assert::same($memberItem->id, $segmentMemberItem->id);
            Assert::same($memberItem->email_address, $segmentMemberItem->email_address);
            dump([$segmentMemberItem, $memberItem]);
        }
    }

    private function getUniqueDummyMail(int $purposeId) : string
    {
        $randomHash = Random::generate();

        return "honza-mailchimptest-{$randomHash}-{$purposeId}@trejbal.land";
    }
}

$test = new ListsTest($container);
$test->run();
