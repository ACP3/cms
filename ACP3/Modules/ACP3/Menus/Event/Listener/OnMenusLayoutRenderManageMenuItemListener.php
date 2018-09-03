<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\I18n\Translator;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Menus\Helpers\MenuItemFormFields;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository;

class OnMenusLayoutRenderManageMenuItemListener
{
    /**
     * @var ACL
     */
    private $acl;
    /**
     * @var View
     */
    private $view;
    /**
     * @var MenuItemRepository
     */
    private $menuItemRepository;
    /**
     * @var MenuItemFormFields
     */
    private $menuItemFormFields;
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var Forms
     */
    private $forms;

    /**
     * OnMenusLayoutRenderManageMenuItemListener constructor.
     *
     * @param ACL                $acl
     * @param Translator         $translator
     * @param View               $view
     * @param Forms              $forms
     * @param MenuItemFormFields $menuItemFormFields
     * @param MenuItemRepository $menuItemRepository
     */
    public function __construct(
        ACL $acl,
        Translator $translator,
        View $view,
        Forms $forms,
        MenuItemFormFields $menuItemFormFields,
        MenuItemRepository $menuItemRepository
    ) {
        $this->acl = $acl;
        $this->view = $view;
        $this->menuItemFormFields = $menuItemFormFields;
        $this->menuItemRepository = $menuItemRepository;
        $this->translator = $translator;
        $this->forms = $forms;
    }

    /**
     * @param TemplateEvent $event
     */
    public function __invoke(TemplateEvent $event)
    {
        $parameters = $event->getParameters();

        if ($this->acl->hasPermission('admin/menus/items/create') === true) {
            $menuItem = $this->fetchMenuItem(!empty($parameters['path']) ? $parameters['path'] : '');
            $formFields = $this->addFormFields($menuItem);

            if (!empty($formFields['blocks'])) {
                $this->view
                    ->assign(
                        'options',
                        $this->fetchCreateMenuItemOption(!empty($menuItem) ? 1 : 0)
                    )
                    ->assign('form', $this->modifyFormValues($menuItem))
                    ->assign($formFields)
                    ->assign('uri_pattern', $parameters['uri_pattern']);

                $this->view->displayTemplate('Menus/Partials/manage_menu_item.tpl');
            }
        }
    }

    /**
     * @param string $routeName
     *
     * @return array
     */
    private function fetchMenuItem($routeName)
    {
        $menuItem = $this->menuItemRepository->getOneMenuItemByUri($routeName);

        if (empty($menuItem)) {
            $menuItem = [];
        }

        return $menuItem;
    }

    /**
     * @param int $currentValue
     *
     * @return array
     */
    private function fetchCreateMenuItemOption($currentValue = 0)
    {
        $createMenuItem = [
            1 => $this->translator->t('menus', 'create_menu_item'),
        ];

        return $this->forms->checkboxGenerator('create_menu_item', $createMenuItem, $currentValue);
    }

    /**
     * @param array $menuItem
     *
     * @return array|null
     */
    private function modifyFormValues(array $menuItem)
    {
        $formData = $this->view->getRenderer()->getTemplateVars('form');

        if (\is_array($formData) && !isset($formData['menu_item_title'])) {
            $formData['menu_item_title'] = !empty($menuItem) ? $menuItem['title'] : '';
        }

        return $formData;
    }

    /**
     * @param array $menuItem
     *
     * @return array
     */
    protected function addFormFields(array $menuItem)
    {
        if (!empty($menuItem)) {
            return $this->menuItemFormFields->createMenuItemFormFields(
                $menuItem['block_id'],
                $menuItem['parent_id'],
                $menuItem['left_id'],
                $menuItem['right_id'],
                $menuItem['display']
            );
        }

        return $this->menuItemFormFields->createMenuItemFormFields();
    }
}
