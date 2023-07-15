<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Tests;

use Nette;
use Tester;
use Tester\Assert;
use Trejjam\MailChimp;

$container = require __DIR__ . '/../bootstrap.php';

final class RequestTest extends Tester\TestCase
{
    private $container;

    public function __construct(Nette\DI\Container $container)
    {
        $this->container = $container;
    }

    public function testConfig() : void
    {
        $api = $this->container->getByType(MailChimp\Request::class);

        Assert::type(MailChimp\Request::class, $api);

        $rootResponseArray = $api->get('/');
        $rootResponseObject = new MailChimp\Entity\Root($rootResponseArray);

        /** @var \Trejjam\MailChimp\Entity\Root $rootResponse */
        $rootResponse = $api->getTyped('/', MailChimp\Entity\Root::class);

        Assert::type(MailChimp\Entity\Root::class, $rootResponse);
        Assert::same($rootResponseObject->account_id, $rootResponse->account_id);
        Assert::notSame('', $rootResponse->last_login);

        $firstLink = $rootResponse->getLinks()[0];

        Assert::type(MailChimp\Entity\Link::class, $firstLink);
        Assert::same($firstLink->rel, 'self');
        Assert::same($firstLink->method, 'GET');
    }
}

$test = new RequestTest($container);
$test->run();
