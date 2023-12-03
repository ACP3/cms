<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace RFM\Event\Api;

use RFM\Repository\ItemData;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * API event. Dispatched each time when file or folder is moved.
 */
class AfterItemMoveEvent extends Event
{
    public const NAME = 'api.after.item.move';

    /**
     * @var ItemData
     */
    protected $itemData;

    /**
     * @var ItemData
     */
    protected $originalItemData;

    public function __construct(ItemData $itemData, ItemData $originalItemData)
    {
        $this->itemData = $itemData;
        $this->originalItemData = $originalItemData;
    }

    public function getItemData(): ItemData
    {
        return $this->itemData;
    }

    public function getOriginalItemData(): ItemData
    {
        return $this->originalItemData;
    }
}
