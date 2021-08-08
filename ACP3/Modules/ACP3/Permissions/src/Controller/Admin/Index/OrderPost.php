<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Controller\InvokableActionInterface;
use ACP3\Core\Http\RedirectResponse;
use ACP3\Modules\ACP3\Permissions\Model\RolesModel;
use ACP3\Modules\ACP3\Permissions\Repository\AclRoleRepository;

class OrderPost extends AbstractWidgetAction implements InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Repository\AclRoleRepository
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
        WidgetContext $context,
        RedirectResponse $redirectResponse,
        AclRoleRepository $roleRepository,
        RolesModel $rolesModel
    ) {
        parent::__construct($context);

        $this->roleRepository = $roleRepository;
        $this->redirectResponse = $redirectResponse;
        $this->rolesModel = $rolesModel;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id, string $action)
    {
        if (($action === 'up' || $action === 'down') && $this->roleRepository->roleExists($id) === true) {
            if ($action === 'up') {
                $this->rolesModel->moveUp($id);
            } else {
                $this->rolesModel->moveDown($id);
            }

            return $this->redirectResponse->temporary('acp/permissions');
        }

        throw new ResultNotExistsException();
    }
}
