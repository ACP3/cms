<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Permissions;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Delete extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private FormAction $actionHelper,
        private Permissions\Model\AclRoleModel $rolesModel
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|JsonResponse|RedirectResponse|Response
     *
     * @throws Exception
     */
    public function __invoke(?string $action = null): array|JsonResponse|RedirectResponse|Response
    {
        return $this->actionHelper->handleCustomDeleteAction(
            $action,
            function (array $items) {
                $bool = $levelNotDeletable = false;

                foreach ($items as $item) {
                    if (\in_array((int) $item, [1, 2, 4], true) === true) {
                        $levelNotDeletable = true;
                    } else {
                        $bool = $this->rolesModel->delete($item);
                    }
                }

                if ($levelNotDeletable === true) {
                    $result = false;
                    $text = $this->translator->t('permissions', 'role_not_deletable');
                } else {
                    $result = $bool !== false;
                    $text = $this->translator->t('system', $result ? 'delete_success' : 'delete_error');
                }

                return $this->actionHelper->setRedirectMessage($result, $text);
            }
        );
    }
}
