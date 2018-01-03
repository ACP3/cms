<?php
/**
 * Copyright (c) by the ACP3 Developers. See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Edit extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Validation\RoleFormValidation
     */
    protected $roleFormValidation;
    /**
     * @var Permissions\Model\AclRolesModel
     */
    protected $rolesModel;
    /**
     * @var Permissions\Model\AclRulesModel
     */
    protected $rulesModel;
    /**
     * @var Core\View\Block\AdminFormBlockInterface
     */
    private $block;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\AdminFormBlockInterface $block
     * @param Permissions\Model\AclRolesModel $rolesModel
     * @param Permissions\Model\AclRulesModel $rulesModel
     * @param \ACP3\Modules\ACP3\Permissions\Validation\RoleFormValidation $roleFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\AdminFormBlockInterface $block,
        Permissions\Model\AclRolesModel $rolesModel,
        Permissions\Model\AclRulesModel $rulesModel,
        Permissions\Validation\RoleFormValidation $roleFormValidation
    ) {
        parent::__construct($context);

        $this->roleFormValidation = $roleFormValidation;
        $this->rolesModel = $rolesModel;
        $this->rulesModel = $rulesModel;
        $this->block = $block;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function execute(int $id)
    {
        return $this->block
            ->setDataById($id)
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost(int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $this->roleFormValidation
                ->setRoleId($id)
                ->validate($formData);

            $formData['parent_id'] = $id === 1 ? 0 : $formData['parent_id'];

            $result = $this->rolesModel->save($formData, $id);
            $this->rulesModel->updateRules($formData['privileges'], $id);

            return $result;
        });
    }
}
