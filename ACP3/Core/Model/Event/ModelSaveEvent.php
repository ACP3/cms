<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model\Event;


use Symfony\Component\EventDispatcher\Event;

class ModelSaveEvent extends Event
{
    /**
     * @var array
     */
    private $data;
    /**
     * @var int|null
     */
    private $entryId;

    /**
     * ModelSaveEvent constructor.
     * @param array $data
     * @param int|null|array $entryId
     */
    public function __construct(array $data, $entryId)
    {
        $this->data = $data;
        $this->entryId = $entryId;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int|null|array
     */
    public function getEntryId()
    {
        return $this->entryId;
    }

    /**
     * @return bool
     */
    public function isDeleteStatement()
    {
        return count($this->data) === 0 && is_array($this->entryId);
    }
}
