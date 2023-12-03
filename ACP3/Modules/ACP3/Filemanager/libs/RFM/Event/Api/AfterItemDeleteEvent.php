<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace RFM\Event\Api;

use RFM\Repository\ItemData;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * API event. Dispatched each time when file or folder is deleted.
 */
class AfterItemDeleteEvent extends Event
{
    public const NAME = 'api.after.item.delete';

    /**
     * @var ItemData
     */
    protected $originalItemData;

    public function __construct(ItemData $originalItemData)
    {
        $this->originalItemData = $originalItemData;
    }

    public function getOriginalItemData(): ItemData
    {
        return $this->originalItemData;
    }
}
