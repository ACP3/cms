<?php

namespace ACP3\Modules\ACP3\Feeds\Validation;

use ACP3\Core;

/**
 * Class AdminFormValidation
 * @package ACP3\Modules\ACP3\Feeds\Validation
 */
class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
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
                    'field' => 'feed_type',
                    'message' => $this->translator->t('feeds', 'select_feed_type'),
                    'extra' => [
                        'haystack' => ['RSS 1.0', 'RSS 2.0', 'ATOM']
                    ]
                ]);

        $this->validator->validate();
    }
}
