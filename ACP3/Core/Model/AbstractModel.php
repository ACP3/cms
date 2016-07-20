<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model;

use ACP3\Core\Model\Event\ModelSaveEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractModel
 * @package ACP3\Core\Model
 */
abstract class AbstractModel
{
    const EVENT_PREFIX = '';

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * AbstractModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param AbstractRepository $repository
     * @param array $data
     * @param null|int $entryId
     * @return int|bool
     */
    protected function save(AbstractRepository $repository, array $data, $entryId = null)
    {
        $this->dispatchBeforeSaveEvent($data, $entryId);

        if (intval($entryId)) {
            $result = $repository->update($data, $entryId);
        } else {
            $result = $repository->insert($data);

            if ($result !== false) {
                $entryId = $result;
            }
        }

        $this->dispatchAfterSaveEvent($data, $entryId);

        return $result;
    }

    /**
     * @param array $data
     * @param int|null $entryId
     */
    private function dispatchBeforeSaveEvent(array $data, $entryId)
    {
        $this->dispatchEvent('core.model.before_save', $data, $entryId);
        $this->dispatchEvent(static::EVENT_PREFIX . '.model.before_save', $data, $entryId);
    }

    private function dispatchEvent($eventName, array $data, $entryId)
    {
        $this->eventDispatcher->dispatch(
            $eventName,
            new ModelSaveEvent($data, $entryId)
        );
    }

    /**
     * @param array $data
     * @param int|null $entryId
     */
    private function dispatchAfterSaveEvent(array $data, $entryId)
    {
        $this->dispatchEvent('core.model.after_save', $data, $entryId);
        $this->dispatchEvent(static::EVENT_PREFIX . '.model.after_save', $data, $entryId);
    }
}
