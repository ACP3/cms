<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\ViewProviders;

use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Menus\Helpers\MenuItemFormFields;

class AdminMenuItemEditViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Helpers\MenuItemFormFields
     */
    private $menuItemFormFieldsHelper;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        Forms $formsHelper,
        FormToken $formTokenHelper,
        MenuItemFormFields $menuItemFormFieldsHelper,
        Modules $modules,
        RequestInterface $request,
        Title $title,
        Translator $translator
    ) {
        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->menuItemFormFieldsHelper = $menuItemFormFieldsHelper;
        $this->modules = $modules;
        $this->request = $request;
        $this->title = $title;
        $this->translator = $translator;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(array $menuItem): array
    {
        $this->title->setPageTitlePrefix($menuItem['title']);

        return array_merge(
            [
                'mode' => $this->fetchMenuItemTypes($menuItem['mode']),
                'modules' => $this->fetchModules($menuItem),
                'target' => $this->formsHelper->linkTargetChoicesGenerator('target', $menuItem['target']),
                'form' => array_merge($menuItem, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken(),
            ],
            $this->menuItemFormFieldsHelper->createMenuItemFormFields(
                $menuItem['block_id'],
                $menuItem['parent_id'],
                $menuItem['left_id'],
                $menuItem['right_id'],
                $menuItem['display']
            )
        );
    }

    private function fetchMenuItemTypes(string $value = ''): array
    {
        $menuItemTypes = [
            1 => $this->translator->t('menus', 'module'),
            2 => $this->translator->t('menus', 'dynamic_page'),
            3 => $this->translator->t('menus', 'hyperlink'),
        ];

        return $this->formsHelper->choicesGenerator('mode', $menuItemTypes, $value);
    }

    private function fetchModules(array $menuItem = []): array
    {
        $modules = [];
        foreach ($this->modules->getAllModulesAlphabeticallySorted() as $info) {
            $modules[$info['name']] = $this->translator->t($info['name'], $info['name']);
        }

        uasort($modules, static function ($a, $b) {
            return $a <=> $b;
        });

        return $this->formsHelper->choicesGenerator('module', $modules, !empty($menuItem) && $menuItem['mode'] == 1 ? $menuItem['uri'] : '');
    }
}
