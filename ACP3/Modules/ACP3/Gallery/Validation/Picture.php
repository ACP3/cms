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
     * @param array $file
     * @param bool  $fileRequired
     *
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validate(array $file, $fileRequired = true)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\PictureValidationRule::NAME,
                [
                    'data' => $file,
                    'field' => 'file',
                    'message' => $this->lang->t('gallery', 'invalid_image_selected'),
                    'extra' => [
                        'required' => $fileRequired
                    ]
                ]);

        $this->validator->validate();
    }
}