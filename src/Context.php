<?php
declare(strict_types=1);

namespace Trejjam\MailChimp;

use Trejjam\MailChimp\Group\Lists;
use Trejjam\MailChimp\Group\Root;

final class Context
{
    private Root $root;
    private Lists $lists;

    public function __construct(
        Root $root,
        Lists $lists
    ) {
        $this->lists = $lists;
        $this->root = $root;
    }

    public function getRootGroup() : Root
    {
        return $this->root;
    }

    public function getListsGroup() : Lists
    {
        return $this->lists;
    }
}
