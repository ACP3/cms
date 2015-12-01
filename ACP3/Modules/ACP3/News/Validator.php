<?php
namespace ACP3\Modules\ACP3\News;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\News\Validator\ValidationRules\ExternalLinkValidationRule;
use ACP3\Modules\ACP3\Seo\Validator\ValidationRules\UriAliasValidationRule;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\News
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Modules
     */
    protected $modules;

    /**
     * Validator constructor.
     *
     * @param \ACP3\Core\Lang                 $lang
     * @param \ACP3\Core\Validator\Validator  $validator
     * @param \ACP3\Core\Modules              $modules
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Validator $validator,
        Core\Modules $modules
    )
    {
        parent::__construct($lang, $validator);

        $this->modules = $modules;
    }

    /**
     * @param array  $formData
     * @param string $uriAlias
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData, $uriAlias = '')
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
                    'message' => $this->lang->t('news', 'title_to_short')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'text',
                    'message' => $this->lang->t('news', 'text_to_short')
                ])
            ->addConstraint(
                Categories\Validator\ValidationRules\CategoryExistsValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['cat', 'cat_create'],
                    'message' => $this->lang->t('news', 'select_category')
                ])
            ->addConstraint(
                ExternalLinkValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['link_title', 'uri', 'target'],
                    'message' => $this->lang->t('news', 'complete_hyperlink_statements')
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
                ]);

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
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NumberGreaterThanValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'readmore_chars',
                    'message' => $this->lang->t('news', 'type_in_readmore_chars'),
                    'extra' => [
                        'value' => 0
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NumberGreaterThanValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'readmore',
                    'message' => $this->lang->t('news', 'select_activate_readmore'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'category_in_breadcrumb',
                    'message' => $this->lang->t('news', 'select_display_category_in_breadcrumb'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ]);

        if ($this->modules->isActive('comments') === true) {
            $this->validator
                ->addConstraint(
                    Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                    [
                        'data' => $formData,
                        'field' => 'comments',
                        'message' => $this->lang->t('news', 'select_allow_comments'),
                        'extra' => [
                            'haystack' => [0, 1]
                        ]
                    ]);
        }

        $this->validator->validate();
    }
}
