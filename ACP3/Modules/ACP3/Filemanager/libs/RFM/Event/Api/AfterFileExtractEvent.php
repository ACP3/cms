<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace RFM\Event\Api;

use RFM\Repository\ItemData;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * API event. Dispatched each time an archive has been extracted.
 */
class AfterFileExtractEvent extends Event
{
    public const NAME = 'api.after.file.extract';

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

    public function getArchiveData(): ItemData
    {
        return $this->itemData;
    }

    /**
     * Return archive content.
     */
    public function getArchiveContent(): array
    {
        return $this->filesList;
    }
}
