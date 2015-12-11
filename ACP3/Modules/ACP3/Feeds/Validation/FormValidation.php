<?php

namespace ACP3\Modules\ACP3\Feeds\Validation;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Feeds\Validation
 */
class FormValidation extends Core\Validation\AbstractFormValidation
{
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
                    'field' => 'feed_type',
                    'message' => $this->translator->t('feeds', 'select_feed_type'),
                    'extra' => [
                        'haystack' => ['RSS 1.0', 'RSS 2.0', 'ATOM']
                    ]
                ]);

        $this->validator->validate();
    }
}
