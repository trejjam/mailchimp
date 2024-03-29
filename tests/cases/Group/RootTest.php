<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Tests\Cases\Group;

use Nette;
use Tester;
use Tester\Assert;
use Trejjam\MailChimp;

$container = require __DIR__ . '/../../bootstrap.php';

final class RootTest extends Tester\TestCase
{
    public function __construct(
        private readonly Nette\DI\Container $container
    ) {
    }

    public function testGetAll() : void
    {
        $groupRoot = $this->container->getByType(MailChimp\Group\Root::class);

        Assert::type(MailChimp\Group\Root::class, $groupRoot);

        $rootEntity = $groupRoot->get();
        Assert::notSame('', $rootEntity->last_login);

        $contactEntity = $rootEntity->getContact();
        Assert::notNull($contactEntity->company);
    }
}

$test = new RootTest($container);
$test->run();
