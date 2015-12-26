<?php
namespace ACP3\Modules\ACP3\Gallery\Validation;

use ACP3\Core;

/**
 * Class Picture
 * @package ACP3\Modules\ACP3\Gallery\Validator
 */
class Picture extends Core\Validation\AbstractFormValidation
{
    /**
     * @param array $formData
     *
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     * @internal param bool $fileRequired
     *
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\PictureValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'file',
                    'message' => $this->translator->t('gallery', 'invalid_image_selected'),
                    'extra' => [
                        'required' => $fileRequired
                    ]
                ]);

        $this->validator->validate();
    }
}