<?php
namespace ACP3\Modules\ACP3\Files\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Files\Validation\ValidationRules\IsExternalFileValidationRule;
use ACP3\Modules\ACP3\Seo\Validation\ValidationRules\UriAliasValidationRule;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Files\Validation
 */
class FormValidation extends Core\Validation\AbstractFormValidation
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
    )
    {
        parent::__construct($translator, $validator);

        $this->modules = $modules;
    }

    /**
     * @param array  $formData
     * @param array  $file
     * @param string $uriAlias
     *
     * @throws \ACP3\Core\Exceptions\InvalidFormToken
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData, $file, $uriAlias = '')
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\DateValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['start', 'end'],
                    'message' => $this->translator->t('system', 'select_date')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('files', 'type_in_title')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'text',
                    'message' => $this->translator->t('files', 'description_to_short')
                ])
            ->addConstraint(
                IsExternalFileValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['external', 'filesize', 'unit'],
                    'message' => $this->translator->t('files', 'type_in_external_resource'),
                    'extra' => [
                        'file' => $file
                    ]
                ])
            ->addConstraint(
                UriAliasValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'alias',
                    'message' => $this->translator->t('seo', 'alias_unallowed_characters_or_exists'),
                    'extra' => [
                        'path' => $uriAlias
                    ]
                ])
            ->addConstraint(
                Categories\Validation\ValidationRules\CategoryExistsValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['cat', 'cat_create'],
                    'message' => $this->translator->t('files', 'select_category')
                ]);

        if (!isset($formData['external'])) {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\FileUploadValidationRule::NAME,
                    [
                        'data' => $file,
                        'field' => 'file_internal',
                        'message' => $this->translator->t('files', 'select_internal_resource'),
                        'extra' => [
                            'required' => empty($uriAlias)
                        ]
                    ]);
        }

        $this->validator->validate();
    }

    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
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
                ]);

        if ($this->modules->isActive('comments')) {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\InArrayValidationRule::NAME,
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
