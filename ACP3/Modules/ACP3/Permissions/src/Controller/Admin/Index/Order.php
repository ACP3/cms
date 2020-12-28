<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Order extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\Repository\RoleRepository
     */
    private $roleRepository;
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Model\RolesModel
     */
    private $rolesModel;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Http\RedirectResponse $redirectResponse,
        Permissions\Model\Repository\RoleRepository $roleRepository,
        Permissions\Model\RolesModel $rolesModel
    ) {
        parent::__construct($context);

        $this->roleRepository = $roleRepository;
        $this->redirectResponse = $redirectResponse;
        $this->rolesModel = $rolesModel;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id, string $action)
    {
        if (($action === 'up' || $action === 'down') && $this->roleRepository->roleExists($id) === true) {
            if ($action === 'up') {
                $this->rolesModel->moveUp($id);
            } else {
                $this->rolesModel->moveDown($id);
            }

            return $this->redirectResponse->temporary('acp/permissions');
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
