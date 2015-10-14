<?php
namespace ACP3\Modules\ACP3\Guestbook;

use ACP3\Core;
use ACP3\Modules\ACP3\Guestbook\Model\GuestbookRepository;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Guestbook
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Core\Validator\Rules\Captcha
     */
    protected $captchaValidator;
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\User
     */
    protected $user;
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Http\Request
     */
    protected $request;
    /**
     * @var \ACP3\Modules\ACP3\Guestbook\Model\GuestbookRepository
     */
    protected $guestbookRepository;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\AccountRepository
     */
    protected $newsletterAccountRepository;

    /**
     * @param \ACP3\Core\Lang                                        $lang
     * @param \ACP3\Core\Validator\Rules\Misc                        $validate
     * @param \ACP3\Core\Validator\Rules\Captcha                     $captchaValidator
     * @param \ACP3\Core\ACL                                         $acl
     * @param \ACP3\Core\User                                        $user
     * @param \ACP3\Core\Date                                        $date
     * @param \ACP3\Core\Modules                                     $modules
     * @param \ACP3\Core\Http\Request                                $request
     * @param \ACP3\Modules\ACP3\Guestbook\Model\GuestbookRepository $guestbookRepository
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Captcha $captchaValidator,
        Core\ACL $acl,
        Core\User $user,
        Core\Date $date,
        Core\Modules $modules,
        Core\Http\Request $request,
        GuestbookRepository $guestbookRepository)
    {
        parent::__construct($lang, $validate);

        $this->captchaValidator = $captchaValidator;
        $this->acl = $acl;
        $this->user = $user;
        $this->date = $date;
        $this->modules = $modules;
        $this->request = $request;
        $this->guestbookRepository = $guestbookRepository;
    }

    /**
     * @param \ACP3\Modules\ACP3\Newsletter\Model\AccountRepository $newsletterAccountRepository
     *
     * @return $this
     */
    public function setNewsletterAccountRepository(Newsletter\Model\AccountRepository $newsletterAccountRepository)
    {
        $this->newsletterAccountRepository = $newsletterAccountRepository;

        return $this;
    }

    /**
     * @param array         $formData
     * @param       boolean $newsletterAccess
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData, $newsletterAccess)
    {
        $this->validateFormKey();

        // Flood Sperre
        $flood = $this->guestbookRepository->getLastDateFromIp($this->request->getServer()->get('REMOTE_ADDR', ''));
        $floodTime = !empty($flood) ? $this->date->timestamp($flood, true) + 30 : 0;
        $time = $this->date->timestamp('now', true);

        $this->errors = [];
        if ($floodTime > $time) {
            $this->errors[] = sprintf($this->lang->t('system', 'flood_no_entry_possible'), $floodTime - $time);
        }
        if (empty($formData['name'])) {
            $this->errors['name'] = $this->lang->t('system', 'name_to_short');
        }
        if (!empty($formData['mail']) && $this->validate->email($formData['mail']) === false) {
            $this->errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if (strlen($formData['message']) < 3) {
            $this->errors['message'] = $this->lang->t('system', 'message_to_short');
        }
        if ($this->acl->hasPermission('frontend/captcha/index/image') === true && $this->user->isAuthenticated() === false && $this->captchaValidator->captcha($formData['captcha']) === false) {
            $this->errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }
        if ($newsletterAccess === true && isset($formData['subscribe_newsletter']) && $formData['subscribe_newsletter'] == 1) {
            if ($this->validate->email($formData['mail']) === false) {
                $this->errors['mail'] = $this->lang->t('guestbook', 'type_in_email_address_to_subscribe_to_newsletter');
            }
            if ($this->validate->email($formData['mail']) === true && $this->newsletterAccountRepository->accountExists($formData['mail']) === true) {
                $this->errors['mail'] = $this->lang->t('newsletter', 'account_exists');
            }
        }

        $this->_checkForFailedValidation();
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
        $this->validateFormKey();

        $this->errors = [];
        if (strlen($formData['message']) < 3) {
            $this->errors['message'] = $this->lang->t('system', 'message_to_short');
        }
        if ($settings['notify'] == 2 && (!isset($formData['active']) || ($formData['active'] != 0 && $formData['active'] != 1))) {
            $this->errors['notify'] = $this->lang->t('guestbook', 'select_activate');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['dateformat']) || ($formData['dateformat'] !== 'long' && $formData['dateformat'] !== 'short')) {
            $this->errors['dateformat'] = $this->lang->t('system', 'select_date_format');
        }
        if (!isset($formData['notify']) || ($formData['notify'] != 0 && $formData['notify'] != 1 && $formData['notify'] != 2)) {
            $this->errors['notify'] = $this->lang->t('guestbook', 'select_notification_type');
        }
        if ($formData['notify'] != 0 && $this->validate->email($formData['notify_email']) === false) {
            $this->errors['notify-email'] = $this->lang->t('system', 'wrong_email_format');
        }
        if (!isset($formData['overlay']) || $formData['overlay'] != 1 && $formData['overlay'] != 0) {
            $this->errors['overlay'] = $this->lang->t('guestbook', 'select_use_overlay');
        }
        if ($this->modules->isActive('emoticons') === true && (!isset($formData['emoticons']) || ($formData['emoticons'] != 0 && $formData['emoticons'] != 1))) {
            $this->errors['emoticons'] = $this->lang->t('guestbook', 'select_emoticons');
        }
        if ($this->modules->isActive('newsletter') === true && (!isset($formData['newsletter_integration']) || ($formData['newsletter_integration'] != 0 && $formData['newsletter_integration'] != 1))) {
            $this->errors['newsletter-integration'] = $this->lang->t('guestbook', 'select_newsletter_integration');
        }

        $this->_checkForFailedValidation();
    }
}
