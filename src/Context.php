<?php
declare(strict_types=1);

namespace Trejjam\MailChimp;

use Trejjam\MailChimp\Group\Lists;
use Trejjam\MailChimp\Group\Root;

final class Context
{
    public function __construct(
        private readonly Root $root,
        private readonly Lists $lists
    ) {
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
