<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Menus\Controller\Admin\Index
 */
class Edit extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Validation\MenuFormValidation
     */
    protected $menuFormValidation;
    /**
     * @var Menus\Model\MenusModel
     */
    protected $menusModel;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param Menus\Model\MenusModel $menusModel
     * @param \ACP3\Modules\ACP3\Menus\Validation\MenuFormValidation $menuFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Menus\Model\MenusModel $menusModel,
        Menus\Validation\MenuFormValidation $menuFormValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->menusModel = $menusModel;
        $this->menuFormValidation = $menuFormValidation;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $menu = $this->menusModel->getOneById($id);

        if (empty($menu) === false) {
            $this->title->setPageTitlePrefix($menu['title']);

            return [
                'form' => array_merge($menu, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost($id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $this->menuFormValidation
                ->setMenuId($id)
                ->validate($formData);

            return $this->menusModel->save($formData, $id);
        });
    }
}
