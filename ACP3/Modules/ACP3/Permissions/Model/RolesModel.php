<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model;


use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Core\NestedSet\NestedSet;
use ACP3\Core\NestedSet\Operation\Edit;
use ACP3\Core\NestedSet\Operation\Insert;
use ACP3\Modules\ACP3\Permissions\Installer\Schema;
use ACP3\Modules\ACP3\Permissions\Model\Repository\RoleRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RolesModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var Secure
     */
    protected $secure;
    /**
     * @var Insert
     */
    protected $insertOperation;
    /**
     * @var Edit
     */
    protected $editOperation;

    /**
     * RoleModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param Secure $secure
     * @param Insert $insertOperation
     * @param Edit $editOperation
     * @param RoleRepository $roleRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Secure $secure,
        Insert $insertOperation,
        Edit $editOperation,
        RoleRepository $roleRepository
    ) {
        parent::__construct($eventDispatcher, $roleRepository);

        $this->secure = $secure;
        $this->insertOperation = $insertOperation;
        $this->editOperation = $editOperation;
    }

    /**
     * @param array $formData
     * @param int|null $entryId
     * @return bool|int
     */
    public function saveRole(array $formData, $entryId = null)
    {
        $data = [
            'name' => $this->secure->strEncode($formData['name']),
            'parent_id' => (int)$formData['parent_id'],
        ];

        return $this->saveNestedSetNode($data, $entryId);
    }

    /**
     * @param $data
     * @param int|null $entryId
     * @return bool|int
     */
    private function saveNestedSetNode(array $data, $entryId)
    {
        $this->dispatchBeforeSaveEvent($this->repository, $data, $entryId);

        if ($entryId === null) {
            $result = $this->insertOperation->execute($data['parent_id'], $data);

            if ($result !== false) {
                $entryId = $result;
            }
        } else {
            $result = $this->editOperation->execute($entryId, $entryId === 1 ? '' : $data['parent_id'], 0, $data);
        }

        $this->dispatchAfterSaveEvent($this->repository, $data, $entryId);

        return $result;
    }
}
