<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\View\Block\Frontend;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Guestbook\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter;
use ACP3\Modules\ACP3\System\Installer\Schema as SystemSchema;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class GuestbookFormBlock extends AbstractFormBlock
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
     * @var Newsletter\Helper\Subscribe
     */
    private $newsletterSubscribeHelper;

    /**
     * GuestbookFormBlock constructor.
     * @param FormBlockContext $context
     * @param SettingsInterface $settings
     * @param UserModel $user
     */
    public function __construct(FormBlockContext $context, SettingsInterface $settings, UserModel $user)
    {
        parent::__construct($context);

        $this->settings = $settings;
        $this->user = $user;
    }

    /**
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\Subscribe $newsletterSubscribeHelper
     *
     * @return $this
     */
    public function setNewsletterSubscribeHelper(Newsletter\Helper\Subscribe $newsletterSubscribeHelper)
    {
        $this->newsletterSubscribeHelper = $newsletterSubscribeHelper;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        return [
            'form' => array_merge($this->getData(), $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken(),
            'can_use_emoticons' => $settings['emoticons'] == 1,
            'subscribe_newsletter' => $this->fetchSubscribeNewsletter($settings)
        ];
    }

    /**
     * @param array $settings
     * @return array
     */
    private function fetchSubscribeNewsletter(array $settings): array
    {
        if ($settings['newsletter_integration'] == 1 && $this->newsletterSubscribeHelper) {
            $newsletterSubscription = [
                1 => $this->translator->t(
                    'guestbook',
                    'subscribe_to_newsletter',
                    ['%title%' => $this->settings->getSettings(SystemSchema::MODULE_NAME)['site_title']]
                )
            ];
            return $this->forms->checkboxGenerator(
                'subscribe_newsletter',
                $newsletterSubscription,
                '1'
            );
        }

        return [];
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
            'website' => '',
            'website_disabled' => false,
            'message' => '',
        ];

        if ($this->user->isAuthenticated() === true) {
            $users = $this->user->getOneById($this->user->getUserId());
            $defaults['name'] = $users['nickname'];
            $defaults['name_disabled'] = true;
            $defaults['mail'] = $users['mail'];
            $defaults['mail_disabled'] = true;
            $defaults['website'] = $users['website'];
            $defaults['website_disabled'] = !empty($users['website']);
        }
        return $defaults;
    }
}
