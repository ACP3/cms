<?php
namespace ACP3\Modules\ACP3\Files;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Files\Validator\ValidationRules\IsExternalFileValidationRule;
use ACP3\Modules\ACP3\Seo\Validator\ValidationRules\UriAliasValidationRule;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Files
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Validator\Validator
     */
    protected $validator;

    /**
     * Validator constructor.
     *
     * @param \ACP3\Core\Lang                 $lang
     * @param \ACP3\Core\Validator\Validator  $validator
     * @param \ACP3\Core\Validator\Rules\Misc $validate
     * @param \ACP3\Core\Modules              $modules
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Validator $validator,
        Core\Validator\Rules\Misc $validate,
        Core\Modules $modules
    )
    {
        parent::__construct($lang, $validate);

        $this->validator = $validator;
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
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\DateValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['start', 'end'],
                    'message' => $this->lang->t('system', 'select_date')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->lang->t('files', 'type_in_title')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'text',
                    'message' => $this->lang->t('files', 'description_to_short')
                ])
            ->addConstraint(
                IsExternalFileValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['external', 'filesize', 'unit'],
                    'message' => $this->lang->t('files', 'type_in_external_resource'),
                    'extra' => [
                        'file' => $file
                    ]
                ])
            ->addConstraint(
                UriAliasValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'alias',
                    'message' => $this->lang->t('seo', 'alias_unallowed_characters_or_exists'),
                    'extra' => [
                        'path' => $uriAlias
                    ]
                ])
            ->addConstraint(
                Categories\Validator\ValidationRules\CategoryExistsValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['cat', 'cat_create'],
                    'message' => $this->lang->t('files', 'select_category')
                ]);

        if (!isset($formData['external'])) {
            $this->validator
                ->addConstraint(
                    Core\Validator\ValidationRules\FileUploadValidationRule::NAME,
                    [
                        'data' => $file,
                        'field' => 'file_internal',
                        'message' => $this->lang->t('files', 'select_internal_resource'),
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
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'dateformat',
                    'message' => $this->lang->t('system', 'select_date_format'),
                    'extra' => [
                        'haystack' => ['long', 'short']
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'sidebar',
                    'message' => $this->lang->t('system', 'select_sidebar_entries')
                ]);

        if ($this->modules->isActive('comments')) {
            $this->validator
                ->addConstraint(
                    Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                    [
                        'data' => $formData,
                        'field' => 'comments',
                        'message' => $this->lang->t('files', 'select_allow_comments'),
                        'extra' => [
                            'haystack' => [0, 1]
                        ]
                    ]);
        }

        $this->validator->validate();
    }
}
