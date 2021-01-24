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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RenderMenuItemManagementFormFieldsListener implements EventSubscriberInterface
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
     * @throws \Doctrine\DBAL\DBALException
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
     * @throws \Doctrine\DBAL\DBALException
     */
    private function fetchMenuItem(string $routeName): array
    {
        $menuItem = $this->menuItemRepository->getOneMenuItemByUri($routeName);

        if (empty($menuItem)) {
            $menuItem = [];
        }

        return $menuItem;
    }

    private function fetchCreateMenuItemOption(int $currentValue = 0): array
    {
        $createMenuItem = [
            1 => $this->translator->t('menus', 'create_menu_item'),
        ];

        return $this->forms->checkboxGenerator('create_menu_item', $createMenuItem, $currentValue);
    }

    private function modifyFormValues(array $menuItem): ?array
    {
        $formData = $this->view->getRenderer()->getTemplateVars('form');

        if (\is_array($formData) && !isset($formData['menu_item_title'])) {
            $formData['menu_item_title'] = !empty($menuItem) ? $menuItem['title'] : '';
        }

        return $formData;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
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
