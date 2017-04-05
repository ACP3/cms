<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Validation;

use ACP3\Core;

/**
 * Class AdminFormValidation
 * @package ACP3\Modules\ACP3\Articles\Validation
 */
class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var string
     */
    protected $uriAlias = '';

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
                    'field' => 'active',
                    'message' => $this->translator->t('articles', 'select_active'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\DateValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['start', 'end'],
                    'message' => $this->translator->t('system', 'select_date')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\MinLengthValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('articles', 'title_to_short'),
                    'extra' => [
                        'length' => 3
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\MinLengthValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'text',
                    'message' => $this->translator->t('articles', 'text_to_short'),
                    'extra' => [
                        'length' => 3
                    ]
                ]);

        $this->validator->dispatchValidationEvent(
            'menus.validation.validate_manage_menu_item',
            $formData
        );

        $this->validator->dispatchValidationEvent(
            'seo.validation.validate_uri_alias',
            $formData,
            ['path' => $this->uriAlias]
        );

        $this->validator->validate();
    }
}
