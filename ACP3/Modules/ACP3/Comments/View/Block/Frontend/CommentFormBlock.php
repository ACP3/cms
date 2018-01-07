<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\View\Block\Frontend;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Comments\Installer\Schema;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class CommentFormBlock extends AbstractFormBlock
{
    /**
     * @var UserModel
     */
    private $user;
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * CommentFormBlock constructor.
     *
     * @param FormBlockContext  $context
     * @param SettingsInterface $settings
     * @param UserModel         $user
     */
    public function __construct(FormBlockContext $context, SettingsInterface $settings, UserModel $user)
    {
        parent::__construct($context);

        $this->user = $user;
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $data = $this->getData();

        return [
            'form' => \array_merge($data, $this->getRequestData()),
            'module' => $data['module'],
            'entry_id' => $data['entryId'],
            'redirect_url' => $data['redirectUrl'],
            'form_token' => $this->formToken->renderFormToken(),
            'can_use_emoticons' => $this->canUseEmoticons(),
        ];
    }

    /**
     * @return bool
     */
    private function canUseEmoticons(): bool
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        return $settings['emoticons'] == 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        $defaults = [
            'name' => '',
            'name_disabled' => false,
            'message' => '',
        ];

        if ($this->user->isAuthenticated() === true) {
            $user = $this->user->getOneById($this->user->getUserId());
            $defaults['name'] = $user['nickname'];
            $defaults['name_disabled'] = true;
            $defaults['message'] = '';
        }

        return $defaults;
    }
}
