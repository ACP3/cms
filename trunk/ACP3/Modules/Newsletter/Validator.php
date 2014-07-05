<?php
namespace ACP3\Modules\Newsletter;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\Newsletter
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Core\Auth
     */
    protected $auth;
    /**
     * @var Model
     */
    protected $newsletterModel;

    public function __construct(Core\Lang $lang, Core\Auth $auth, Model $newsletterModel)
    {
        parent::__construct($lang);

        $this->auth = $auth;
        $this->newsletterModel = $newsletterModel;
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('newsletter', 'subject_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $this->lang->t('newsletter', 'text_to_short') . strlen($formData['text']);
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateSubscribe(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (Core\Validate::email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if (Core\Validate::email($formData['mail']) && $this->newsletterModel->accountExists($formData['mail']) === true) {
            $errors['mail'] = $this->lang->t('newsletter', 'account_exists');
        }
        if (Core\Modules::hasPermission('frontend/captcha/index/image') === true && $this->auth->isUser() === false && Core\Validate::captcha($formData['captcha']) === false) {
            $errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateUnsubscribe(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (Core\Validate::email($formData['mail']) === false) {
            $errors[] = $this->lang->t('system', 'wrong_email_format');
        }
        if (Core\Validate::email($formData['mail']) && $this->newsletterModel->accountExists($formData['mail']) === false) {
            $errors[] = $this->lang->t('newsletter', 'account_not_exists');
        }
        if (Core\Modules::hasPermission('frontend/captcha/index/image') === true && $this->auth->isUser() === false && Core\Validate::captcha($formData['captcha']) === false) {
            $errors[] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (Core\Validate::email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param $mail
     * @param $hash
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateActivate($mail, $hash)
    {
        $errors = array();
        if ($this->newsletterModel->accountExists($mail, $hash) === false) {
            $errors[] = $this->lang->t('newsletter', 'account_not_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }


} 