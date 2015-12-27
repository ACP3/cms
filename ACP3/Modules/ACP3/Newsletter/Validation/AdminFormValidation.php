<?php
namespace ACP3\Modules\ACP3\Newsletter\Validation;

use ACP3\Core;

/**
 * Class AdminFormValidation
 * @package ACP3\Modules\ACP3\Newsletter\Validation
 */
class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('newsletter', 'subject_to_short')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'text',
                    'message' => $this->translator->t('newsletter', 'text_to_short')
                ]);

        $this->validator->validate();
    }
}
