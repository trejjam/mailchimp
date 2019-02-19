<?php
declare(strict_types=1);

namespace Trejjam\MailChimp;

use Trejjam;

final class Context
{
    /**
     * @var Group\Root
     */
    private $root;
    /**
     * @var Group\Lists
     */
    private $lists;

    public function __construct(
        Trejjam\MailChimp\Group\Root $root,
        Trejjam\MailChimp\Group\Lists $lists
    ) {
        $this->root = $root;
        $this->lists = $lists;
    }

    public function getRootGroup() : Group\Root
    {
        return $this->root;
    }

    public function getListsGroup() : Group\Lists
    {
        return $this->lists;
    }
}
