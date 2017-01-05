<?php

namespace Trejjam\MailChimp\Tests\Group;

use Nette;
use Tester;
use Tester\Assert;
use Trejjam\MailChimp;

$container = require __DIR__ . '/../bootstrap.php';

class RootTest extends Tester\TestCase
{
	private $container;

	function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
	}

	public function testConfig()
	{
		/** @var MailChimp\Group\Root $groupRoot */
		$groupRoot = $this->container->getByType(MailChimp\Group\Root::class);

		Assert::type(MailChimp\Group\Root::class, $groupRoot);

		$rootEntity = $groupRoot->get();
		Assert::type(MailChimp\Entity\Root::class, $rootEntity);
		Assert::notSame('', $rootEntity->last_login);
	}
}

$test = new RootTest($container);
$test->run();
