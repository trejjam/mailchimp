<?php

namespace Trejjam\MailChimp\Tests\Group;

use Nette\DI\Container;
use Nette\Utils\Random;
use Tester\TestCase;
use Tester\Assert;
use Trejjam\MailChimp;

$container = require __DIR__ . '/../../bootstrap.php';

final class ListsTest extends TestCase
{
    const TEST_LIST = 'testList';
    private $container;

    function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function testGetAll() : void
    {
        /** @var MailChimp\Group\Lists $groupLists */
        $groupLists = $this->container->getByType(MailChimp\Group\Lists::class);

        Assert::type(MailChimp\Group\Lists::class, $groupLists);

        $listsEntity = $groupLists->getAll();
        Assert::type(MailChimp\Entity\Lists\Lists::class, $listsEntity);

        $listItems = $listsEntity->getLists();

        if ($listItems->count() > 0) {
            /** @var MailChimp\Entity\Lists\ListItem $listItem */
            $listItem = $listItems->current();

            Assert::type(MailChimp\Entity\Lists\ListItem::class, $listItem);
            Assert::notSame(null, $listItem->id);
            Assert::type(MailChimp\Entity\Contact::class, $listItem->getContact());
            Assert::type(MailChimp\Entity\Link::class, $listItem->getLinks()->current());
        }
    }

    public function testGetEntity() : void
    {
        /** @var MailChimp\Group\Lists $groupLists */
        $groupLists = $this->container->getByType(MailChimp\Group\Lists::class);

        Assert::type(MailChimp\Group\Lists::class, $groupLists);

        Assert::throws(function () use ($groupLists) {
            $groupLists->get('not_exist_id');
        }, MailChimp\Exception\ListNotFoundException::class);

        $listsEntity = $groupLists->getAll();
        if ($listsEntity->getLists()->count() > 0) {
            $_listEntity = $listsEntity->getLists()->current();

            $listEntity = $groupLists->get($_listEntity->id);
            Assert::type(MailChimp\Entity\Lists\ListItem::class, $listEntity);
            Assert::notSame(null, $listEntity->id);
            Assert::type(MailChimp\Entity\Contact::class, $listEntity->getContact());
            Assert::type(MailChimp\Entity\Link::class, $listEntity->getLinks()->current());
        }
    }

    public function testGetEntityMembers() : void
    {
        /** @var MailChimp\Group\Lists $groupLists */
        $groupLists = $this->container->getByType(MailChimp\Group\Lists::class);

        Assert::throws(function () use ($groupLists) {
            $groupLists->getMembers('not_exist_id');
        }, MailChimp\Exception\ListNotFoundException::class);

        $listsEntity = $groupLists->getAll();
        if ($listsEntity->getLists()->count() > 0) {
            $_listEntity = $listsEntity->getLists()->current();

            $listMembers = $groupLists->getMembers($_listEntity->id);
            Assert::type(MailChimp\Entity\Lists\Member\Lists::class, $listMembers);
            Assert::type(MailChimp\Entity\Link::class, $listMembers->getLinks()->current());
            Assert::same($_listEntity->id, $listMembers->list_id);

            $listMemberItems = $listMembers->getMembers();

            if ($listMemberItems->count() > 0) {
                /** @var MailChimp\Entity\Lists\Member\MemberItem $listMemberItem */
                $listMemberItem = $listMemberItems->current();
                Assert::type(MailChimp\Entity\Lists\Member\MemberItem::class, $listMemberItem);
                Assert::type(MailChimp\Entity\Link::class, $listMemberItem->getLinks()->current());
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
        /** @var MailChimp\Group\Lists $groupLists */
        $groupLists = $this->container->getByType(MailChimp\Group\Lists::class);
        /** @var MailChimp\Lists $lists */
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

        if ($listMemberItems->count() > 0) {
            /** @var MailChimp\Entity\Lists\Member\MemberItem $_listMemberItem */
            $_listMemberItem = $listMemberItems->current();

            Assert::throws(function () use ($groupLists, $_listMemberItem) {
                $groupLists->getMember($_listMemberItem->list_id, 'not_exist_id');
            }, MailChimp\Exception\MemberNotFoundException::class);

            Assert::throws(function () use ($groupLists, $_listMemberItem) {
                $groupLists->getMember('not_exist_id', $_listMemberItem->id);
            }, MailChimp\Exception\MemberNotFoundException::class);

            /** @var MailChimp\Entity\Lists\Member\MemberItem $listMemberItem */
            $listMemberItem = $groupLists->getMember($_listMemberItem->list_id, $_listMemberItem->id);

            Assert::type(MailChimp\Entity\Lists\Member\MemberItem::class, $listMemberItem);
            Assert::type(MailChimp\Entity\Link::class, $listMemberItem->getLinks()->current());
        }

        $groupLists->removeMember($memberItem3);
    }

    public function testAddUpdateDeleteEntityMember() : void
    {
        /** @var MailChimp\Group\Lists $groupLists */
        $groupLists = $this->container->getByType(MailChimp\Group\Lists::class);
        /** @var MailChimp\Lists $lists */
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
        }, MailChimp\Exception\MemberNotFoundException::class, "Member '{$memberItem1->id}' not found in list '{$memberItem1->list_id}' not found");

        $groupLists->removeMember($memberItemGet2);
        Assert::throws(function () use ($groupLists, $memberItem1) {
            $groupLists->getMember($memberItem1->list_id, $memberItem1->id);
        }, MailChimp\Exception\MemberNotFoundException::class, "Member '{$memberItem1->id}' not found in list '{$memberItem1->list_id}' not found");

        Assert::throws(function () use ($groupLists, $memberItem2) {
            $groupLists->getMember($memberItem2->list_id, $memberItem2->id);
        }, MailChimp\Exception\MemberNotFoundException::class, "Member '{$memberItem2->id}' not found in list '{$memberItem2->list_id}' not found");
    }

    public function testGetEntitySegments() : void
    {
        /** @var MailChimp\Group\Lists $groupLists */
        $groupLists = $this->container->getByType(MailChimp\Group\Lists::class);

        Assert::throws(function () use ($groupLists) {
            $groupLists->getSegments('not_exist_id');
        }, MailChimp\Exception\ListNotFoundException::class);

        $listsEntity = $groupLists->getAll();
        if ($listsEntity->getLists()->count() > 0) {
            /** @var MailChimp\Entity\Lists\ListItem $_listEntity */
            $_listEntity = $listsEntity->getLists()->current();

            $listSegments = $groupLists->getSegments($_listEntity->id);
            Assert::type(MailChimp\Entity\Lists\Segment\Lists::class, $listSegments);
            Assert::type(MailChimp\Entity\Link::class, $listSegments->getLinks()->current());
            Assert::same($_listEntity->id, $listSegments->list_id);

            $listSegmentItems = $listSegments->getSegments();

            if ($listSegmentItems->count() > 0) {
                /** @var MailChimp\Entity\Lists\Segment\Segment $listSegmentItem */
                $listSegmentItem = $listSegmentItems->current();

                Assert::type(MailChimp\Entity\Lists\Segment\Segment::class, $listSegmentItem);
                Assert::type(MailChimp\Entity\Link::class, $listSegmentItem->getLinks()->current());
                Assert::same($_listEntity->id, $listSegmentItem->list_id);

                $segment = $groupLists->getSegment($_listEntity->id, $listSegmentItem->id);

                Assert::same($listSegmentItem->id, $segment->id);
            }
        }
    }

    public function testAddSegmentMember() : void
    {
        /** @var MailChimp\Group\Lists $groupLists */
        $groupLists = $this->container->getByType(MailChimp\Group\Lists::class);
        /** @var MailChimp\Lists $lists */
        $lists = $this->container->getByType(MailChimp\Lists::class);

        $testList = $lists->getListByName(self::TEST_LIST);

        $listSegments = $groupLists->getSegments($testList);
        Assert::type(MailChimp\Entity\Lists\Segment\Lists::class, $listSegments);
        Assert::type(MailChimp\Entity\Link::class, $listSegments->getLinks()->current());
        Assert::same($testList, $listSegments->list_id);

        $listSegmentItems = $listSegments->getSegments();

        if ($listSegmentItems->count() > 0) {
            /** @var MailChimp\Entity\Lists\Segment\Segment $listSegmentItem */
            $listSegmentItem = $listSegmentItems->current();

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

        return "honza+mailchimptest-{$randomHash}-{$purposeId}@trejbal.land";
    }
}

$test = new ListsTest($container);
$test->run();
