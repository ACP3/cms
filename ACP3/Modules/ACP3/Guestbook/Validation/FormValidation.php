<?php
namespace ACP3\Modules\ACP3\Guestbook\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha\Validation\ValidationRules\CaptchaValidationRule;
use ACP3\Modules\ACP3\Guestbook\Validation\ValidationRules\FloodBarrierValidationRule;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Guestbook\Validation
 */
class FormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var string
     */
    protected $ipAddress = '';
    /**
     * @var bool
     */
    protected $newsletterAccess = false;

    /**
     * @param string $ipAddress
     *
     * @return $this
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    /**
     * @param boolean $newsletterAccess
     *
     * @return $this
     */
    public function setNewsletterAccess($newsletterAccess)
    {
        $this->newsletterAccess = (bool)$newsletterAccess;
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
                FloodBarrierValidationRule::class,
                [
                    'message' => $this->translator->t('system', 'flood_no_entry_possible'),
                    'extra' => [
                        'ip' => $this->ipAddress
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'name',
                    'message' => $this->translator->t('system', 'name_to_short')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'message',
                    'message' => $this->translator->t('system', 'message_to_short')
                ])
            ->addConstraint(
                CaptchaValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'captcha',
                    'message' => $this->translator->t('captcha', 'invalid_captcha_entered')
                ]);

        if (!empty($formData['mail'])) {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\EmailValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'mail',
                        'message' => $this->translator->t('system', 'wrong_email_format')
                    ]);
        }

        if ($this->newsletterAccess === true && isset($formData['subscribe_newsletter'])) {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\EmailValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'mail',
                        'message' => $this->translator->t('guestbook',
                            'type_in_email_address_to_subscribe_to_newsletter')
                    ])
                ->addConstraint(
                    Newsletter\Validation\ValidationRules\AccountExistsValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'mail',
                        'message' => $this->translator->t('newsletter', 'account_exists')
                    ]
                );
        }

        $this->validator->validate();
    }
}
