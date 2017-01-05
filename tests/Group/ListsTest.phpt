<?php

namespace Trejjam\MailChimp\Tests\Group;

use Nette;
use Tester;
use Tester\Assert;
use Trejjam\MailChimp;

$container = require __DIR__ . '/../bootstrap.php';

class ListsTest extends Tester\TestCase
{
	private $container;

	function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
	}

	public function testConfig()
	{
		/** @var MailChimp\Group\Lists $groupLists */
		$groupLists = $this->container->getByType(MailChimp\Group\Lists::class);

		Assert::type(MailChimp\Group\Lists::class, $groupLists);

		$listsEntity = $groupLists->get();
		Assert::type(MailChimp\Entity\Lists\Lists::class, $listsEntity);

		$listItems = $listsEntity->getLists();
		if ($listItems->count() > 0) {
			$listItem = $listItems->current();
			Assert::notSame('', $listItem->id);
		}
	}
}

$test = new ListsTest($container);
$test->run();
