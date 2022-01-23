<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\EventListener;

use ACP3\Core\ACL;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\I18n\Translator;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Menus\Helpers\MenuItemFormFields;
use ACP3\Modules\ACP3\Menus\Repository\MenuItemRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RenderMenuItemManagementFormFieldsListener implements EventSubscriberInterface
{
    public function __construct(private ACL $acl, private Translator $translator, private View $view, private Forms $forms, private MenuItemFormFields $menuItemFormFields, private MenuItemRepository $menuItemRepository)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(TemplateEvent $event): void
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
                    ->assign('titleFormFieldName', 'menu_item_title')
                    ->assign('uri_pattern', $parameters['uri_pattern']);

                $event->addContent($this->view->fetchTemplate('Menus/Partials/tab_menu_item_fields.tpl'));
            }
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function fetchMenuItem(string $routeName): array
    {
        $menuItem = $this->menuItemRepository->getOneMenuItemByUri($routeName);

        if (empty($menuItem)) {
            $menuItem = [];
        }

        return $menuItem;
    }

    /**
     * @return array<string, mixed>[]
     */
    private function fetchCreateMenuItemOption(int $currentValue = 0): array
    {
        $createMenuItem = [
            1 => $this->translator->t('menus', 'create_menu_item'),
        ];

        return $this->forms->checkboxGenerator('create_menu_item', $createMenuItem, $currentValue);
    }

    /**
     * @param array<string, mixed> $menuItem
     *
     * @return array<string, mixed>|null
     */
    private function modifyFormValues(array $menuItem): ?array
    {
        $formData = $this->view->getRenderer()->getTemplateVars('form');

        if (\is_array($formData) && !isset($formData['menu_item_title'])) {
            $formData['menu_item_title'] = !empty($menuItem) ? $menuItem['title'] : '';
        }

        return $formData;
    }

    /**
     * @param array<string, mixed> $menuItem
     *
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    protected function addFormFields(array $menuItem): array
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

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'core.layout.form_extension' => ['__invoke', 255],
        ];
    }
}
