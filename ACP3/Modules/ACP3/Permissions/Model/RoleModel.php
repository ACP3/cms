<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model;


use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Core\NestedSet\NestedSet;
use ACP3\Modules\ACP3\Permissions\Installer\Schema;
use ACP3\Modules\ACP3\Permissions\Model\Repository\RoleRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RoleModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var Secure
     */
    protected $secure;
    /**
     * @var NestedSet
     */
    protected $nestedSet;
    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * RoleModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param Secure $secure
     * @param NestedSet $nestedSet
     * @param RoleRepository $roleRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Secure $secure,
        NestedSet $nestedSet,
        RoleRepository $roleRepository
    ) {
        parent::__construct($eventDispatcher);

        $this->secure = $secure;
        $this->nestedSet = $nestedSet;
        $this->roleRepository = $roleRepository;
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
        $this->dispatchBeforeSaveEvent($this->roleRepository, $data, $entryId);

        if ($entryId === null) {
            $result = $this->nestedSet->insertNode(
                $data['parent_id'],
                $data,
                RoleRepository::TABLE_NAME,
                true
            );

            if ($result !== false) {
                $entryId = $result;
            }
        } else {
            $result = $this->nestedSet->editNode(
                $entryId,
                $entryId === 1 ? '' : $data['parent_id'],
                0,
                $data,
                RoleRepository::TABLE_NAME
            );
        }

        $this->dispatchAfterSaveEvent($this->roleRepository, $data, $entryId);

        return $result;
    }
}
