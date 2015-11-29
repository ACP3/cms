<?php
namespace ACP3\Modules\ACP3\Gallery\Validator;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo\Validator\ValidationRules\UriAliasValidationRule;

/**
 * Class Gallery
 * @package ACP3\Modules\ACP3\Gallery\Validator
 */
class Gallery extends Core\Validator\AbstractValidator
{
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
                    'message' => $this->lang->t('gallery', 'type_in_gallery_title')
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
}
