<?php
/**
 * Copyright (c) by the ACP3 Developers. See the LICENSE file at the top-level module directory for licencing
 * details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

class Create extends AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Validation\MenuItemFormValidation
     */
    protected $menuItemFormValidation;
    /**
     * @var Menus\Model\MenuItemsModel
     */
    protected $menuItemsModel;
    /**
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;

    /**
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\FormBlockInterface $block
     * @param Menus\Model\MenuItemsModel $menuItemsModel
     * @param \ACP3\Modules\ACP3\Menus\Validation\MenuItemFormValidation $menuItemFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\FormBlockInterface $block,
        Menus\Model\MenuItemsModel $menuItemsModel,
        Menus\Validation\MenuItemFormValidation $menuItemFormValidation
    ) {
        parent::__construct($context);

        $this->menuItemFormValidation = $menuItemFormValidation;
        $this->menuItemsModel = $menuItemsModel;
        $this->block = $block;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return $this->block
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handleSaveAction(
            function () {
                $formData = $this->request->getPost()->all();

                $this->menuItemFormValidation->validate($formData);

                $formData['mode'] = $this->fetchMenuItemModeForSave($formData);
                $formData['uri'] = $this->fetchMenuItemUriForSave($formData);
                return $this->menuItemsModel->save($formData);
            },
            'acp/menus'
        );
    }
}
