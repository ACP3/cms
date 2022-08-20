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
use ACP3\Modules\ACP3\Menus\Enum\PageTypeEnum;
use ACP3\Modules\ACP3\Menus\Helpers\MenuItemFormFields;

class AdminMenuItemEditViewProvider
{
    public function __construct(private readonly Forms $formsHelper, private readonly FormToken $formTokenHelper, private readonly MenuItemFormFields $menuItemFormFieldsHelper, private readonly Modules $modules, private readonly RequestInterface $request, private readonly Title $title, private readonly Translator $translator)
    {
    }

    /**
     * @param array<string, mixed> $menuItem
     *
     * @return array<string, mixed>
     *
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

    /**
     * @return array<string, mixed>[]
     */
    private function fetchMenuItemTypes(string $value = ''): array
    {
        $menuItemTypes = [
            PageTypeEnum::MODULE->value => $this->translator->t('menus', 'module'),
            PageTypeEnum::DYNAMIC_PAGE->value => $this->translator->t('menus', 'dynamic_page'),
            PageTypeEnum::HYPERLINK->value => $this->translator->t('menus', 'hyperlink'),
        ];

        return $this->formsHelper->choicesGenerator('mode', $menuItemTypes, $value);
    }

    /**
     * @param array<string, mixed> $menuItem
     *
     * @return array<string, mixed>[]
     */
    private function fetchModules(array $menuItem = []): array
    {
        $modules = [];
        foreach ($this->modules->getAllModulesAlphabeticallySorted() as $info) {
            $modules[$info['name']] = $this->translator->t($info['name'], $info['name']);
        }

        uasort($modules, static fn ($a, $b) => $a <=> $b);

        return $this->formsHelper->choicesGenerator(
            'module',
            $modules,
            !empty($menuItem) && PageTypeEnum::tryFrom((int) $menuItem['mode']) === PageTypeEnum::MODULE ? $menuItem['uri'] : ''
        );
    }
}
