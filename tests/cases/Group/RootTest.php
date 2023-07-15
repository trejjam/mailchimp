<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Tests\Group;

use Nette;
use Tester;
use Tester\Assert;
use Trejjam\MailChimp;

$container = require __DIR__ . '/../../bootstrap.php';

final class RootTest extends Tester\TestCase
{
    private $container;

    public function __construct(Nette\DI\Container $container)
    {
        $this->container = $container;
    }

    public function testGetAll() : void
    {
        $groupRoot = $this->container->getByType(MailChimp\Group\Root::class);

        Assert::type(MailChimp\Group\Root::class, $groupRoot);

        $rootEntity = $groupRoot->get();
        Assert::type(MailChimp\Entity\Root::class, $rootEntity);
        Assert::notSame('', $rootEntity->last_login);

        $contactEntity = $rootEntity->getContact();
        Assert::type(MailChimp\Entity\Contact::class, $contactEntity);
        Assert::notSame(null, $contactEntity->company);
    }
}

$test = new RootTest($container);
$test->run();
