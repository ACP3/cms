<?php
namespace ACP3\Modules\ACP3\Gallery\Validation;

use ACP3\Core;

/**
 * Class AdminSettingsFormValidation
 * @package ACP3\Modules\ACP3\Gallery\Validation
 */
class AdminSettingsFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;

    /**
     * AdminSettingsFormValidation constructor.
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
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'dateformat',
                    'message' => $this->translator->t('system', 'select_date_format'),
                    'extra' => [
                        'haystack' => ['long', 'short']
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'sidebar',
                    'message' => $this->translator->t('system', 'select_sidebar_entries')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'overlay',
                    'message' => $this->translator->t('gallery', 'select_use_overlay'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'thumbwidth',
                    'message' => $this->translator->t('gallery', 'invalid_image_width_entered')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'width',
                    'message' => $this->translator->t('gallery', 'invalid_image_width_entered')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'thumbheight',
                    'message' => $this->translator->t('gallery', 'invalid_image_height_entered')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'height',
                    'message' => $this->translator->t('gallery', 'invalid_image_height_entered')
                ]);

        if ($this->modules->isActive('comments') === true) {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\InArrayValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'comments',
                        'message' => $this->translator->t('gallery', 'select_allow_comments'),
                        'extra' => [
                            'haystack' => [0, 1]
                        ]
                    ]);
        }

        $this->validator->validate();
    }
}
