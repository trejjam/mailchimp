<?php
declare(strict_types=1);

namespace Trejjam\MailChimp;

use Trejjam\MailChimp\Group\Root;
use Trejjam\MailChimp\Group\Lists;

final class Context
{
    /**
     * @var Root
     */
    private $root;
    /**
     * @var Lists
     */
    private $lists;

    public function __construct(
        Root $root,
        Lists $lists
    ) {
        $this->root = $root;
        $this->lists = $lists;
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
