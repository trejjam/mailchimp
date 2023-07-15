<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Group;

use Nette\Utils\JsonException;
use Trejjam\MailChimp\Entity\Root as EntityRoot;
use Trejjam\MailChimp\Request;

final class Root
{
    public function __construct(
        private readonly Request $apiRequest
    ) {
    }

    /**
     * @throws JsonException
     */
    public function get() : EntityRoot
    {
        return $this->apiRequest->getTyped('/', EntityRoot::class);
    }
}
