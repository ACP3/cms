<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace RFM\Event\Api;

use RFM\Repository\ItemData;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * API event. Dispatched each time new files have been downloaded.
 */
class AfterItemDownloadEvent extends Event
{
    public const NAME = 'api.after.item.download';

    /**
     * @var ItemData
     */
    protected $itemData;

    public function __construct(ItemData $itemData)
    {
        $this->itemData = $itemData;
    }

    public function getDownloadedItemData(): ItemData
    {
        return $this->itemData;
    }
}
