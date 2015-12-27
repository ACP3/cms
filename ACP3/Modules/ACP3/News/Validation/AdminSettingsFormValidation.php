<?php
namespace ACP3\Modules\ACP3\News\Validation;

use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;

/**
 * Class AdminSettingsFormValidation
 * @package ACP3\Modules\ACP3\News\Validation
 */
class AdminSettingsFormValidation extends AbstractFormValidation
{
    /**
     * @var Core\Modules
     */
    protected $modules;

    /**
     * Validator constructor.
     *
     * @param \ACP3\Core\I18n\Translator      $translator
     * @param \ACP3\Core\Validation\Validator $validator
     * @param \ACP3\Core\Modules              $modules
     */
    public function __construct(
        Core\I18n\Translator $translator,
        Core\Validation\Validator $validator,
        Core\Modules $modules
    ) {
        parent::__construct($translator, $validator);

        $this->modules = $modules;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'dateformat',
                    'message' => $this->translator->t('system', 'select_date_format'),
                    'extra' => [
                        'haystack' => ['long', 'short']
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'sidebar',
                    'message' => $this->translator->t('system', 'select_sidebar_entries')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NumberGreaterThanValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'readmore_chars',
                    'message' => $this->translator->t('news', 'type_in_readmore_chars'),
                    'extra' => [
                        'value' => 0
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NumberGreaterThanValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'readmore',
                    'message' => $this->translator->t('news', 'select_activate_readmore'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'category_in_breadcrumb',
                    'message' => $this->translator->t('news', 'select_display_category_in_breadcrumb'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ]);

        if ($this->modules->isActive('comments') === true) {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                    [
                        'data' => $formData,
                        'field' => 'comments',
                        'message' => $this->translator->t('news', 'select_allow_comments'),
                        'extra' => [
                            'haystack' => [0, 1]
                        ]
                    ]);
        }

        $this->validator->validate();
    }
}