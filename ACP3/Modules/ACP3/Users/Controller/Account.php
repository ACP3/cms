<?php

namespace ACP3\Modules\ACP3\Users\Controller;

use ACP3\Core;
use ACP3\Core\Helpers\Country;
use ACP3\Modules\ACP3\Users;

/**
 * Class Account
 * @package ACP3\Modules\ACP3\Users\Controller
 */
class Account extends Core\Modules\FrontendController
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model
     */
    protected $usersModel;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validator
     */
    protected $usersValidator;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext $context
     * @param \ACP3\Core\Date                               $date
     * @param \ACP3\Core\Helpers\FormToken                  $formTokenHelper
     * @param \ACP3\Core\Helpers\Secure                     $secureHelper
     * @param \ACP3\Modules\ACP3\Users\Model                $usersModel
     * @param \ACP3\Modules\ACP3\Users\Validator            $usersValidator
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Secure $secureHelper,
        Users\Model $usersModel,
        Users\Validator $usersValidator)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->secureHelper = $secureHelper;
        $this->usersModel = $usersModel;
        $this->usersValidator = $usersValidator;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if ($this->auth->isUser() === false || $this->get('core.validator.rules.misc')->isNumber($this->auth->getUserId()) === false) {
            $this->redirect()->temporary('users/index/login');
        }
    }

    public function actionEdit()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $this->_editPost($this->request->getPost()->getAll());
        }

        $user = $this->auth->getUserInfo();

        $this->view->assign('contact', $this->get('users.helpers.forms')->fetchContactDetails(
            $user['mail'],
            $user['website'],
            $user['icq'],
            $user['skype']
        ));
        $this->view->assign(
            $this->get('users.helpers.forms')->fetchUserProfileFormFields(
                $user['birthday'],
                $user['country'],
                $user['gender']
            )
        );

        $this->view->assign('form', array_merge($user, $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken();
    }

    public function actionSettings()
    {
        $settings = $this->config->getSettings('users');

        if ($this->request->getPost()->isEmpty() === false) {
            $this->_settingsPost($this->request->getPost()->getAll(), $settings);
        }

        $user = $this->usersModel->getOneById($this->auth->getUserId());

        $this->view->assign('language_override', $settings['language_override']);
        $this->view->assign('entries_override', $settings['entries_override']);
        $this->view->assign(
            $this->get('users.helpers.forms')->fetchUserSettingsFormFields(
                (int)$user['entries'],
                $user['language'],
                $user['time_zone'],
                $user['address_display'],
                $user['birthday_display'],
                $user['country_display'],
                $user['mail_display']
            )
        );

        $this->view->assign('form', array_merge($user, $this->request->getPost()->getAll()));

        $this->formTokenHelper->generateFormToken();
    }

    public function actionIndex()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            $updateValues = [
                'draft' => Core\Functions::strEncode($this->request->getPost()->get('draft', ''), true)
            ];
            $bool = $this->usersModel->update($updateValues, $this->auth->getUserId());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'));
        }

        $user = $this->usersModel->getOneById($this->auth->getUserId());

        $this->view->assign('draft', $user['draft']);
    }

    /**
     * @param array $formData
     */
    protected function _editPost(array $formData)
    {
        $this->handlePostAction(
            function () use ($formData) {
                $this->usersValidator->validateEditProfile($formData);

                $updateValues = [
                    'nickname' => Core\Functions::strEncode($formData['nickname']),
                    'realname' => Core\Functions::strEncode($formData['realname']),
                    'gender' => (int)$formData['gender'],
                    'birthday' => $formData['birthday'],
                    'mail' => $formData['mail'],
                    'website' => Core\Functions::strEncode($formData['website']),
                    'icq' => $formData['icq'],
                    'skype' => Core\Functions::strEncode($formData['skype']),
                    'street' => Core\Functions::strEncode($formData['street']),
                    'house_number' => Core\Functions::strEncode($formData['house_number']),
                    'zip' => Core\Functions::strEncode($formData['zip']),
                    'city' => Core\Functions::strEncode($formData['city']),
                    'country' => Core\Functions::strEncode($formData['country']),
                ];

                // Neues Passwort
                if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat'])) {
                    $salt = $this->secureHelper->salt(Core\Auth::SALT_LENGTH);
                    $newPassword = $this->secureHelper->generateSaltedPassword($salt, $formData['new_pwd'], 'sha512');
                    $updateValues['pwd'] = $newPassword;
                    $updateValues['pwd_salt'] = $salt;
                }

                $bool = $this->usersModel->update($updateValues, $this->auth->getUserId());

                $user = $this->usersModel->getOneById($this->auth->getUserId());
                $this->auth->setRememberMeCookie(
                    $this->auth->getUserId(),
                    $user['remember_me_token'],
                    Core\Auth::REMEMBER_ME_COOKIE_LIFETIME
                );

                $this->formTokenHelper->unsetFormToken();

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'));
            }
        );
    }

    /**
     * @param array $formData
     * @param array $settings
     */
    protected function _settingsPost(array $formData, array $settings)
    {
        $this->handlePostAction(
            function () use ($formData, $settings) {
                $this->usersValidator->validateUserSettings($formData, $settings);

                $updateValues = [
                    'mail_display' => (int)$formData['mail_display'],
                    'birthday_display' => (int)$formData['birthday_display'],
                    'address_display' => (int)$formData['address_display'],
                    'country_display' => (int)$formData['country_display'],
                    'date_format_long' => Core\Functions::strEncode($formData['date_format_long']),
                    'date_format_short' => Core\Functions::strEncode($formData['date_format_short']),
                    'time_zone' => $formData['date_time_zone'],
                ];
                if ($settings['language_override'] == 1) {
                    $updateValues['language'] = $formData['language'];
                }
                if ($settings['entries_override'] == 1) {
                    $updateValues['entries'] = (int)$formData['entries'];
                }

                $bool = $this->usersModel->update($updateValues, $this->auth->getUserId());

                $this->formTokenHelper->unsetFormToken();

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'settings_success' : 'settings_error'));
            }
        );
    }
}
