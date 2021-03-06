<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\EventListener;

use ACP3\Core\ACL;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Core\Validation\ValidationRules\InArrayValidationRule;
use ACP3\Core\Validation\ValidationRules\IntegerValidationRule;
use ACP3\Core\Validation\ValidationRules\NotEmptyValidationRule;
use ACP3\Modules\ACP3\Menus\Validation\ValidationRules\AllowedMenuValidationRule;
use ACP3\Modules\ACP3\Menus\Validation\ValidationRules\ParentIdValidationRule;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ValidateMenuItemFormFieldsListener implements EventSubscriberInterface
{
    /**
     * @var ACL
     */
    private $acl;
    /**
     * @var Translator
     */
    private $translator;

    public function __construct(ACL $acl, Translator $translator)
    {
        $this->acl = $acl;
        $this->translator = $translator;
    }

    public function __invoke(FormValidationEvent $event)
    {
        $formData = $event->getFormData();

        if (isset($formData['create_menu_item']) === true && $this->acl->hasPermission('admin/menus/items/create') === true) {
            $event
                ->getValidator()
                ->addConstraint(
                    NotEmptyValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'menu_item_title',
                        'message' => $this->translator->t('menus', 'title_to_short'),
                    ]
                )
                ->addConstraint(
                    IntegerValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'block_id',
                        'message' => $this->translator->t('menus', 'select_menu_bar'),
                    ]
                )
                ->addConstraint(
                    ParentIdValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'parent_id',
                        'message' => $this->translator->t('menus', 'select_superior_page'),
                    ]
                )
                ->addConstraint(
                    InArrayValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'display',
                        'message' => $this->translator->t('menus', 'select_item_visibility'),
                        'extra' => [
                            'haystack' => [0, 1],
                        ],
                    ]
                )
                ->addConstraint(
                    AllowedMenuValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => ['parent_id', 'block_id'],
                        'message' => $this->translator->t('menus', 'superior_page_not_allowed'),
                    ]
                );
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'core.validation.form_extension' => '__invoke',
        ];
    }
}
