<?php
namespace ACP3\Modules\ACP3\Emoticons\Validation;

use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;

/**
 * Class AdminSettingsFormValidation
 * @package ACP3\Modules\ACP3\Emoticons\Validation
 */
class AdminSettingsFormValidation extends AbstractFormValidation
{
    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'width',
                    'message' => $this->translator->t('emoticons', 'invalid_image_width_entered')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'height',
                    'message' => $this->translator->t('emoticons', 'invalid_image_height_entered')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'filesize',
                    'message' => $this->translator->t('emoticons', 'invalid_image_filesize_entered')
                ]);

        $this->validator->validate();
    }
}