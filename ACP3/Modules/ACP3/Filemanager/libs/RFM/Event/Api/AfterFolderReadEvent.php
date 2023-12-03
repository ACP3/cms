<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace RFM\Event\Api;

use RFM\Repository\ItemData;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * API event. Dispatched each time a folder contents is read.
 */
class AfterFolderReadEvent extends Event
{
    public const NAME = 'api.after.folder.read';

    /**
     * @var ItemData
     */
    protected $itemData;

    /**
     * @var array
     */
    protected $filesList;

    public function __construct(ItemData $itemData, array $filesList)
    {
        $this->itemData = $itemData;
        $this->filesList = $filesList;
    }

    public function getFolderData(): ItemData
    {
        return $this->itemData;
    }

    /**
     * Return folder content.
     */
    public function getFolderContent(): array
    {
        return $this->filesList;
    }
}
