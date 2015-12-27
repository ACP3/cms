<?php
namespace ACP3\Modules\ACP3\Guestbook\Validation;

use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;

/**
 * Class AdminFormValidation
 * @package ACP3\Modules\ACP3\Guestbook\Validation
 */
class AdminFormValidation extends AbstractFormValidation
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
    public function setSettings(array $settings)
    {
        $this->settings = $settings;

        return $this;
    }

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
                    'field' => 'message',
                    'message' => $this->translator->t('system', 'message_to_short')
                ]);

        if ($this->settings['notify'] == 2) {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                    [
                        'data' => $formData,
                        'field' => 'active',
                        'message' => $this->translator->t('guestbook', 'select_activate'),
                        'extra' => [
                            'haystack' => [0, 1]
                        ]
                    ]);
        }

        $this->validator->validate();
    }
}