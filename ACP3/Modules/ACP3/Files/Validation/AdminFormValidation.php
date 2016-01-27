<?php
namespace ACP3\Modules\ACP3\Files\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Files\Validation\ValidationRules\IsExternalFileValidationRule;
use ACP3\Modules\ACP3\Seo\Validation\ValidationRules\UriAliasValidationRule;

/**
 * Class AdminFormValidation
 * @package ACP3\Modules\ACP3\Files\Validation
 */
class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var string
     */
    protected $uriAlias = '';
    /**
     * @var null|array
     */
    protected $file;

    /**
     * @param string $uriAlias
     *
     * @return $this
     */
    public function setUriAlias($uriAlias)
    {
        $this->uriAlias = $uriAlias;
        return $this;
    }

    /**
     * @param array|null $file
     *
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\DateValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['start', 'end'],
                    'message' => $this->translator->t('system', 'select_date')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('files', 'type_in_title')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'text',
                    'message' => $this->translator->t('files', 'description_to_short')
                ])
            ->addConstraint(
                IsExternalFileValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['external', 'filesize', 'unit'],
                    'message' => $this->translator->t('files', 'type_in_external_resource'),
                    'extra' => [
                        'file' => $this->file
                    ]
                ])
            ->addConstraint(
                UriAliasValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'alias',
                    'message' => $this->translator->t('seo', 'alias_unallowed_characters_or_exists'),
                    'extra' => [
                        'path' => $this->uriAlias
                    ]
                ])
            ->addConstraint(
                Categories\Validation\ValidationRules\CategoryExistsValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['cat', 'cat_create'],
                    'message' => $this->translator->t('files', 'select_category')
                ]);

        if (!isset($formData['external'])) {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\FileUploadValidationRule::class,
                    [
                        'data' => $this->file,
                        'field' => 'file_internal',
                        'message' => $this->translator->t('files', 'select_internal_resource'),
                        'extra' => [
                            'required' => empty($this->uriAlias)
                        ]
                    ]);
        }

        $this->validator->validate();
    }
}
