<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Group;

use Nette\Utils\JsonException;
use Trejjam\MailChimp\Request;
use Trejjam\MailChimp\Entity\Root as EntityRoot;

final class Root
{
    /**
     * @var Request
     */
    private $apiRequest;

    function __construct(Request $apiRequest)
    {
        $this->apiRequest = $apiRequest;
    }

    /**
     * @throws JsonException
     */
    public function get() : EntityRoot
    {
        return $this->apiRequest->get('/', EntityRoot::class);
    }
}
