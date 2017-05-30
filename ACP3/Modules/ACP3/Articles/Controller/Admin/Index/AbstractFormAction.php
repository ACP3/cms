<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;

abstract class AbstractFormAction extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Helpers\ManageMenuItem
     */
    protected $manageMenuItemHelper;
    /**
     * @var ACLInterface
     */
    private $acl;

    /**
     * AbstractFormAction constructor.
     * @param Context\FrontendContext $context
     * @param ACLInterface $acl
     */
    public function __construct(Context\FrontendContext $context, ACLInterface $acl)
    {
        parent::__construct($context);

        $this->acl = $acl;
    }

    /**
     * @param \ACP3\Modules\ACP3\Menus\Helpers\ManageMenuItem $manageMenuItemHelper
     *
     * @return $this
     */
    public function setManageMenuItemHelper(Menus\Helpers\ManageMenuItem $manageMenuItemHelper)
    {
        $this->manageMenuItemHelper = $manageMenuItemHelper;

        return $this;
    }

    /**
     * @param array $formData
     * @param int $articleId
     */
    protected function createOrUpdateMenuItem(array $formData, $articleId)
    {
        if ($this->acl->hasPermission('admin/menus/items/create') === true) {
            $data = [
                'mode' => 4,
                'block_id' => $formData['block_id'],
                'parent_id' => (int)$formData['parent_id'],
                'display' => $formData['display'],
                'title' => $formData['menu_item_title'],
                'target' => 1
            ];

            $this->manageMenuItemHelper->manageMenuItem(
                sprintf(Articles\Helpers::URL_KEY_PATTERN, $articleId),
                isset($formData['create']) === true,
                $data
            );
        }
    }
}
