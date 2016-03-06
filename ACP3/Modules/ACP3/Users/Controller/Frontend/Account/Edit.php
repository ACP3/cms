<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Account;

use ACP3\Core;
use ACP3\Modules\ACP3\Users;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Users\Controller\Frontend\Account
 */
class Edit extends AbstractAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserRepository
     */
    protected $userRepository;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\AccountFormValidation
     */
    protected $accountFormValidation;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext             $context
     * @param \ACP3\Core\Helpers\FormToken                              $formTokenHelper
     * @param \ACP3\Core\Helpers\Secure                                 $secureHelper
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository             $userRepository
     * @param \ACP3\Modules\ACP3\Users\Validation\AccountFormValidation $accountFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Secure $secureHelper,
        Users\Model\UserRepository $userRepository,
        Users\Validation\AccountFormValidation $accountFormValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->secureHelper = $secureHelper;
        $this->userRepository = $userRepository;
        $this->accountFormValidation = $accountFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->executePost($this->request->getPost()->all());
        }

        $user = $this->user->getUserInfo();

        $this->view->assign(
            $this->get('users.helpers.forms')->fetchUserProfileFormFields(
                $user['birthday'],
                $user['country'],
                $user['gender']
            )
        );

        return [
            'contact' => $this->get('users.helpers.forms')->fetchContactDetails(
                $user['mail'],
                $user['website'],
                $user['icq'],
                $user['skype']
            ),
            'form' => array_merge($user, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($formData) {
                $this->accountFormValidation
                    ->setUserId($this->user->getUserId())
                    ->validate($formData);

                $updateValues = [
                    'nickname' => $this->get('core.helpers.secure')->strEncode($formData['nickname']),
                    'realname' => $this->get('core.helpers.secure')->strEncode($formData['realname']),
                    'gender' => (int)$formData['gender'],
                    'birthday' => $formData['birthday'],
                    'mail' => $formData['mail'],
                    'website' => $this->get('core.helpers.secure')->strEncode($formData['website']),
                    'icq' => $formData['icq'],
                    'skype' => $this->get('core.helpers.secure')->strEncode($formData['skype']),
                    'street' => $this->get('core.helpers.secure')->strEncode($formData['street']),
                    'house_number' => $this->get('core.helpers.secure')->strEncode($formData['house_number']),
                    'zip' => $this->get('core.helpers.secure')->strEncode($formData['zip']),
                    'city' => $this->get('core.helpers.secure')->strEncode($formData['city']),
                    'country' => $this->get('core.helpers.secure')->strEncode($formData['country']),
                ];

                // Neues Passwort
                if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat'])) {
                    $salt = $this->secureHelper->salt(Core\User::SALT_LENGTH);
                    $newPassword = $this->secureHelper->generateSaltedPassword($salt, $formData['new_pwd'], 'sha512');
                    $updateValues['pwd'] = $newPassword;
                    $updateValues['pwd_salt'] = $salt;
                }

                $bool = $this->userRepository->update($updateValues, $this->user->getUserId());

                $user = $this->userRepository->getOneById($this->user->getUserId());
                $this->user->setRememberMeCookie(
                    $this->user->getUserId(),
                    $user['remember_me_token'],
                    Core\User::REMEMBER_ME_COOKIE_LIFETIME
                );

                $this->formTokenHelper->unsetFormToken();

                return $this->redirectMessages()->setMessage($bool,
                    $this->translator->t('system', $bool !== false ? 'edit_success' : 'edit_error'));
            }
        );
    }
}
