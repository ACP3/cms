<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace RFM\Event\Api;

use RFM\Repository\ItemData;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * API event. Dispatched each time a folder contents is sought.
 */
class AfterFolderSeekEvent extends Event
{
    public const NAME = 'api.after.folder.seek';

    /**
     * @var ItemData
     */
    protected $itemData;

    /**
     * @var string
     */
    protected $searchString;

    /**
     * @var array
     */
    protected $filesList;

    public function __construct(ItemData $itemData, string $searchString, array $filesList)
    {
        $this->itemData = $itemData;
        $this->searchString = $searchString;
        $this->filesList = $filesList;
    }

    public function getFolderData(): ItemData
    {
        return $this->itemData;
    }

    public function getSearchString(): string
    {
        return $this->searchString;
    }

    /**
     * Return a list of files found.
     */
    public function getSearchResult(): array
    {
        return $this->filesList;
    }
}
