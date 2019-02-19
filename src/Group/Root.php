<?php

namespace Trejjam\MailChimp\Group;

use Nette;
use Trejjam;

final class Root
{
    /**
     * @var Trejjam\MailChimp\Request
     */
    private $apiRequest;

    function __construct(Trejjam\MailChimp\Request $apiRequest)
    {
        $this->apiRequest = $apiRequest;
    }

    /**
     * @throws Nette\Utils\JsonException
     */
    public function get() : Trejjam\MailChimp\Entity\Root
    {
        return $this->apiRequest->get('/', Trejjam\MailChimp\Entity\Root::class);
    }
}
