<?php
namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;

/**
 * Class AccountSettingsFormValidation
 * @package ACP3\Modules\ACP3\Users\Validation
 */
class AccountSettingsFormValidation extends AbstractUserFormValidation
{
    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @param array $settings
     *
     * @return $this
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class);

        parent::validateUserSettings($formData, $this->settings['language_override'],
            $this->settings['entries_override']);

        $this->validator->validate();
    }
}