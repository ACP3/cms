<?php
namespace ACP3\Modules\ACP3\Gallery\Validation;

use ACP3\Core;

/**
 * Class PictureFormValidation
 * @package ACP3\Modules\ACP3\Gallery\Validation
 */
class PictureFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var bool
     */
    protected $fileRequired = false;

    /**
     * @param boolean $fileRequired
     *
     * @return $this
     */
    public function setFileRequired($fileRequired)
    {
        $this->fileRequired = (bool)$fileRequired;
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
                Core\Validation\ValidationRules\PictureValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'file',
                    'message' => $this->translator->t('gallery', 'invalid_image_selected'),
                    'extra' => [
                        'required' => $this->fileRequired
                    ]
                ]);

        $this->validator->validate();
    }
}