<?php

namespace Test;

use Nette,
	Tester,
	Tester\Assert,
	Trejjam\MailChimp;

$container = require __DIR__ . '/bootstrap.php';


class DITest extends Tester\TestCase
{
	private $container;

	function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
	}

	public function testConfig()
	{
		/** @var MailChimp\DI\MailChimpExtension $mailChimpExtension */
		$mailChimpExtension = new MailChimp\DI\MailChimpExtension;
		$reflection = new \ReflectionClass($mailChimpExtension);
		$reflectionMethod = $reflection->getMethod('createConfig');
		$reflectionMethod->setAccessible(TRUE);

		$mailChimpExtension->setConfig([
			'apiKey' => 'someApiKey123-us11',
			'lists'  => [
				'newsletter' => 'foo123',
				'user'       => 123,
			]
		]);
		$mailChimpExtension->setCompiler(new Nette\DI\Compiler, 'container_' . __FUNCTION__);
		$mailChimpConfig = $reflectionMethod->invoke($mailChimpExtension);

		Assert::same([
			'findDc' => TRUE,
			'apiUrl' => 'https://us11.api.mailchimp.com/3.0/',
			'apiKey' => 'someApiKey123-us11',
			'lists'  => [
				'newsletter' => 'foo123',
				'user'       => 123,
			],
		], $mailChimpConfig);
	}
}

$test = new DITest($container);
$test->run();
