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
        $this->dispatchBeforeSaveEvent($repository, $data, $entryId);

        if (intval($entryId)) {
            $result = $repository->update($data, $entryId);
        } else {
            $result = $repository->insert($data);

            if ($result !== false) {
                $entryId = $result;
            }
        }

        $this->dispatchAfterSaveEvent($repository, $data, $entryId);

        return $result;
    }

    /**
     * @param AbstractRepository $repository
     * @param array $data
     * @param int|null $entryId
     */
    protected function dispatchBeforeSaveEvent(AbstractRepository $repository, array $data, $entryId)
    {
        $this->dispatchEvent('core.model.before_save', $data, $entryId);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.before_save',
            $data,
            $entryId
        );
    }

    /**
     * @param string $eventName
     * @param array $data
     * @param int|null $entryId
     */
    private function dispatchEvent($eventName, array $data, $entryId)
    {
        $this->eventDispatcher->dispatch(
            $eventName,
            new ModelSaveEvent($data, $entryId)
        );
    }

    /**
     * @param AbstractRepository $repository
     * @param array $data
     * @param int|null $entryId
     */
    protected function dispatchAfterSaveEvent(AbstractRepository $repository, array $data, $entryId)
    {
        $this->dispatchEvent('core.model.after_save', $data, $entryId);
        $this->dispatchEvent(
            static::EVENT_PREFIX . '.model.' . $repository::TABLE_NAME . '.after_save',
            $data,
            $entryId
        );
    }
}
