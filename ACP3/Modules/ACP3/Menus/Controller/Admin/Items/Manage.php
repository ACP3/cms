<?php
/**
 * Copyright (c) by the ACP3 Developers. See the LICENSE file at the top-level module directory for licencing
 * details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Items;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus;

class Manage extends Core\Controller\AbstractFrontendAction
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
     * @var Core\View\Block\RepositoryAwareFormBlockInterface
     */
    private $block;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\RepositoryAwareFormBlockInterface $block
     * @param Menus\Model\MenuItemsModel $menuItemsModel
     * @param \ACP3\Modules\ACP3\Menus\Validation\MenuItemFormValidation $menuItemFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\RepositoryAwareFormBlockInterface $block,
        Menus\Model\MenuItemsModel $menuItemsModel,
        Menus\Validation\MenuItemFormValidation $menuItemFormValidation
    ) {
        parent::__construct($context);

        $this->menuItemFormValidation = $menuItemFormValidation;
        $this->menuItemsModel = $menuItemsModel;
        $this->block = $block;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function execute(?int $id)
    {
        return $this->block
            ->setDataById($id)
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @param int|null $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost(?int $id)
    {
        return $this->actionHelper->handleSaveAction(
            function () use ($id) {
                $formData = $this->request->getPost()->all();

                $this->menuItemFormValidation->validate($formData);

                $formData['mode'] = $this->fetchMenuItemModeForSave($formData);
                $formData['uri'] = $this->fetchMenuItemUriForSave($formData);
                return $this->menuItemsModel->save($formData, $id);
            },
            'acp/menus'
        );
    }

    /**
     * @param array $formData
     *
     * @return string
     */
    protected function fetchMenuItemModeForSave(array $formData)
    {
        return ($formData['mode'] == 2 || $formData['mode'] == 3) && preg_match(
            Menus\Helpers\MenuItemsList::ARTICLES_URL_KEY_REGEX,
            $formData['uri']
        ) ? '4' : $formData['mode'];
    }

    /**
     * @param array $formData
     *
     * @return string
     */
    protected function fetchMenuItemUriForSave(array $formData)
    {
        return $formData['mode'] == 1 ? $formData['module'] : ($formData['mode'] == 4 ? sprintf(
            Articles\Helpers::URL_KEY_PATTERN,
            $formData['articles']
        ) : $formData['uri']);
    }
}
