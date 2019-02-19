<?php

namespace Trejjam\MailChimp\Tests;

use Nette;
use Tester;
use Tester\Assert;
use Trejjam\MailChimp;

$container = require __DIR__ . '/../bootstrap.php';

final class ContextTest extends Tester\TestCase
{
    private $container;

    function __construct(Nette\DI\Container $container)
    {
        $this->container = $container;
    }

    public function testConfig() : void
    {
        /** @var MailChimp\Context $mailchimpContext */
        $mailchimpContext = $this->container->getByType(MailChimp\Context::class);

        Assert::type(MailChimp\Context::class, $mailchimpContext);

        /** @var MailChimp\Entity\Root $rootResponse */
        $rootResponse = $mailchimpContext->getRootGroup()->get();

        Assert::type(MailChimp\Entity\Root::class, $rootResponse);

        /** @var MailChimp\Entity\Lists\Lists $listsResponse */
        $listsResponse = $mailchimpContext->getListsGroup()->getAll();

        Assert::type(MailChimp\Entity\Lists\Lists::class, $listsResponse);
    }
}

$test = new ContextTest($container);
$test->run();
