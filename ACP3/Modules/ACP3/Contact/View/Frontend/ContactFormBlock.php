<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Contact\View\Frontend;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Contact\Installer\Schema;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class ContactFormBlock extends AbstractFormBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var UserModel
     */
    private $user;

    /**
     * ContactFormBlock constructor.
     * @param FormBlockContext $context
     * @param SettingsInterface $settings
     * @param UserModel $user
     */
    public function __construct(
        FormBlockContext $context,
        SettingsInterface $settings,
        UserModel $user
    ) {
        parent::__construct($context);

        $this->settings = $settings;
        $this->user = $user;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $copy = [
            1 => $this->translator->t('contact', 'send_copy_to_sender'),
        ];

        return [
            'form' => \array_merge($this->getData(), $this->getRequestData()),
            'copy' => $this->forms->checkboxGenerator('copy', $copy, 0),
            'contact' => $this->settings->getSettings(Schema::MODULE_NAME),
            'form_token' => $this->formToken->renderFormToken(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        $defaults = [
            'name' => '',
            'name_disabled' => false,
            'mail' => '',
            'mail_disabled' => false,
            'message' => '',
        ];

        if ($this->user->isAuthenticated() === true) {
            $user = $this->user->getOneById($this->user->getUserId());
            $defaults['name'] = !empty($user['realname']) ? $user['realname'] : $user['nickname'];
            $defaults['name_disabled'] = true;
            $defaults['mail'] = $user['mail'];
            $defaults['mail_disabled'] = true;
        }

        return $defaults;
    }
}
