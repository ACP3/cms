<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Core\Validation\ValidationRules\InArrayValidationRule;
use ACP3\Core\Validation\ValidationRules\IntegerValidationRule;
use ACP3\Core\Validation\ValidationRules\NotEmptyValidationRule;
use ACP3\Modules\ACP3\Menus\Validation\ValidationRules\AllowedMenuValidationRule;
use ACP3\Modules\ACP3\Menus\Validation\ValidationRules\ParentIdValidationRule;

class OnMenusValidationValidateManageMenuItem
{
    /**
     * @var ACL
     */
    private $acl;
    /**
     * @var Translator
     */
    private $translator;

    /**
     * OnMenusValidationValidateManageMenuItem constructor.
     * @param ACL $acl
     * @param Translator $translator
     */
    public function __construct(ACL $acl, Translator $translator)
    {
        $this->acl = $acl;
        $this->translator = $translator;
    }

    /**
     * @param FormValidationEvent $event
     */
    public function validateManageMenuItem(FormValidationEvent $event)
    {
        $formData = $event->getFormData();

        if ($this->acl->hasPermission('admin/menus/items/create') === true && isset($formData['create_menu_item']) === true) {
            $event
                ->getValidator()
                ->addConstraint(
                    NotEmptyValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'menu_item_title',
                        'message' => $this->translator->t('menus', 'title_to_short')
                    ]
                )
                ->addConstraint(
                    IntegerValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'block_id',
                        'message' => $this->translator->t('menus', 'select_menu_bar')
                    ])
                ->addConstraint(
                    ParentIdValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'parent_id',
                        'message' => $this->translator->t('menus', 'select_superior_page')
                    ])
                ->addConstraint(
                    InArrayValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'display',
                        'message' => $this->translator->t('menus', 'select_item_visibility'),
                        'extra' => [
                            'haystack' => [0, 1]
                        ]
                    ])
                ->addConstraint(
                    AllowedMenuValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => ['parent_id', 'block_id'],
                        'message' => $this->translator->t('menus', 'superior_page_not_allowed')
                    ]);
        }
    }
}
