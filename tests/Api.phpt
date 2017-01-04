<?php

namespace Test;

use Nette;
use Tester;
use Tester\Assert;
use Trejjam\MailChimp;

$container = require __DIR__ . '/bootstrap.php';

class ApiTest extends Tester\TestCase
{
	private $container;

	function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
	}

	public function testConfig()
	{
		$api = $this->container->getByType(MailChimp\Api::class);

		Assert::type(MailChimp\Api::class, $api);
	}
}

$test = new ApiTest($container);
$test->run();
