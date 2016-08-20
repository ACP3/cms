<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Model;


use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractNestedSetModel;
use ACP3\Core\NestedSet\Operation\Delete;
use ACP3\Core\NestedSet\Operation\Edit;
use ACP3\Core\NestedSet\Operation\Insert;
use ACP3\Modules\ACP3\Menus\Installer\Schema;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MenuItemsModel extends AbstractNestedSetModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var Secure
     */
    protected $secure;

    /**
     * MenuItemsModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param MenuItemRepository $repository
     * @param Insert $insertOperation
     * @param Edit $editOperation
     * @param Delete $deleteOperation
     * @param Secure $secure
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        MenuItemRepository $repository,
        Insert $insertOperation,
        Edit $editOperation,
        Delete $deleteOperation,
        Secure $secure
    ) {
        parent::__construct($eventDispatcher, $repository, $insertOperation, $editOperation, $deleteOperation);

        $this->secure = $secure;
    }

    /**
     * @param array $formData
     * @param null|int $entryId
     * @return bool|int
     */
    public function saveMenuItem(array $formData, $entryId = null)
    {
        $data = [
            'mode' => (int)$formData['mode'],
            'block_id' => (int)$formData['block_id'],
            'parent_id' => (int)$formData['parent_id'],
            'display' => $formData['display'],
            'title' => $this->secure->strEncode($formData['title']),
            'uri' => $formData['uri'],
            'target' => $formData['display'] == 0 ? 1 : $formData['target'],
        ];

        return $this->save($data, $entryId);
    }
}
