<?php
namespace ACP3\Modules\ACP3\Files\Validation;

use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;

/**
 * Class AdminSettingsFormValidation
 * @package ACP3\Modules\ACP3\Files\Validation
 */
class AdminSettingsFormValidation extends AbstractFormValidation
{
    /**
     * @var \ACP3\Core\Modules
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
                ]);

        if ($this->modules->isActive('comments')) {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\InArrayValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'comments',
                        'message' => $this->translator->t('files', 'select_allow_comments'),
                        'extra' => [
                            'haystack' => [0, 1]
                        ]
                    ]);
        }

        $this->validator->validate();
    }
}