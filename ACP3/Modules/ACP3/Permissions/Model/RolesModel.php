<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model;


use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractNestedSetModel;
use ACP3\Core\NestedSet\Operation\Delete;
use ACP3\Core\NestedSet\Operation\Edit;
use ACP3\Core\NestedSet\Operation\Insert;
use ACP3\Modules\ACP3\Permissions\Installer\Schema;
use ACP3\Modules\ACP3\Permissions\Model\Repository\RoleRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RolesModel extends AbstractNestedSetModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var Secure
     */
    protected $secure;

    /**
     * RoleModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param RoleRepository $roleRepository
     * @param Insert $insertOperation
     * @param Edit $editOperation
     * @param Delete $deleteOperation
     * @param Secure $secure
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        RoleRepository $roleRepository,
        Insert $insertOperation,
        Edit $editOperation,
        Delete $deleteOperation,
        Secure $secure
    ) {
        parent::__construct($eventDispatcher, $roleRepository, $insertOperation, $editOperation, $deleteOperation);

        $this->secure = $secure;
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

        return $this->save($data, $entryId);
    }
}
