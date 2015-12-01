<?php
namespace ACP3\Modules\ACP3\Guestbook;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha\Validator\ValidationRules\CaptchaValidationRule;
use ACP3\Modules\ACP3\Guestbook\Validator\ValidationRules\FloodBarrierValidationRule;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Guestbook
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;

    /**
     * Validator constructor.
     *
     * @param \ACP3\Core\Lang                 $lang
     * @param \ACP3\Core\Validator\Validator  $validator
     * @param \ACP3\Core\Modules              $modules
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Validator $validator,
        Core\Modules $modules)
    {
        parent::__construct($lang, $validator);

        $this->modules = $modules;
    }

    /**
     * @param array   $formData
     * @param boolean $newsletterAccess
     * @param string  $ipAddress
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData, $newsletterAccess, $ipAddress)
    {
        $this->validator
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                FloodBarrierValidationRule::NAME,
                [
                    'message' => $this->lang->t('system', 'flood_no_entry_possible'),
                    'extra' => [
                        'ip' => $ipAddress
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'name',
                    'message' => $this->lang->t('system', 'name_to_short')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'message',
                    'message' => $this->lang->t('system', 'message_to_short')
                ])
            ->addConstraint(
                CaptchaValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'captcha',
                    'message' => $this->lang->t('captcha', 'invalid_captcha_entered')
                ]);

        if (!empty($formData['mail'])) {
            $this->validator
                ->addConstraint(
                    Core\Validator\ValidationRules\EmailValidationRule::NAME,
                    [
                        'data' => $formData,
                        'field' => 'mail',
                        'message' => $this->lang->t('system', 'wrong_email_format')
                    ]);
        }

        if ($newsletterAccess === true && isset($formData['subscribe_newsletter'])) {
            $this->validator
                ->addConstraint(
                    Core\Validator\ValidationRules\EmailValidationRule::NAME,
                    [
                        'data' => $formData,
                        'field' => 'mail',
                        'message' => $this->lang->t('guestbook', 'type_in_email_address_to_subscribe_to_newsletter')
                    ])
                ->addConstraint(
                    Newsletter\Validator\ValidationRules\AccountExistsValidationRule::NAME,
                    [
                        'data' => $formData,
                        'field' => 'mail',
                        'message' => $this->lang->t('newsletter', 'account_exists')
                    ]
                );
        }

        $this->validator->validate();
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateEdit(array $formData, array $settings)
    {
        $this->validator
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'message',
                    'message' => $this->lang->t('system', 'message_to_short')
                ]);

        if ($settings['notify'] == 2) {
            $this->validator
                ->addConstraint(
                    Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                    [
                        'data' => $formData,
                        'field' => 'active',
                        'message' => $this->lang->t('guestbook', 'select_activate'),
                        'extra' => [
                            'haystack' => [0, 1]
                        ]
                    ]);
        }

        $this->validator->validate();
    }

    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'dateformat',
                    'message' => $this->lang->t('system', 'select_date_format'),
                    'extra' => [
                        'haystack' => ['long', 'short']
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'notify',
                    'message' => $this->lang->t('guestbook', 'select_notification_type'),
                    'extra' => [
                        'haystack' => [0, 1, 2]
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'overlay',
                    'message' => $this->lang->t('guestbook', 'select_use_overlay'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ]);

        if ($formData['notify'] != 0) {
            $this->validator
                ->addConstraint(
                    Core\Validator\ValidationRules\EmailValidationRule::NAME,
                    [
                        'data' => $formData,
                        'field' => 'notify_email',
                        'message' => $this->lang->t('system', 'wrong_email_format')
                    ]);
        }

        if ($this->modules->isActive('emoticons') === true) {
            $this->validator
                ->addConstraint(
                    Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                    [
                        'data' => $formData,
                        'field' => 'emoticons',
                        'message' => $this->lang->t('guestbook', 'select_emoticons'),
                        'extra' => [
                            'haystack' => [0, 1]
                        ]
                    ]);
        }

        if ($this->modules->isActive('newsletter') === true) {
            $this->validator
                ->addConstraint(
                    Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                    [
                        'data' => $formData,
                        'field' => 'newsletter_integration',
                        'message' => $this->lang->t('guestbook', 'select_newsletter_integration'),
                        'extra' => [
                            'haystack' => [0, 1]
                        ]
                    ]);
        }
    }
}
