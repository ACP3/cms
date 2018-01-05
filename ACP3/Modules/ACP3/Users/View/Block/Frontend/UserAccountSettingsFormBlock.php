<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\View\Block\Frontend;

use ACP3\Core\View\Block\AbstractRepositoryAwareFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Users\Helpers\Forms;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class UserAccountSettingsFormBlock extends AbstractRepositoryAwareFormBlock
{
    /**
     * @var Forms
     */
    private $userFormsHelper;

    /**
     * UserAccountSettingsFormBlock constructor.
     * @param FormBlockContext $context
     * @param UserModel $userModel
     * @param Forms $userFormsHelper
     */
    public function __construct(FormBlockContext $context, UserModel $userModel, Forms $userFormsHelper)
    {
        parent::__construct($context, $userModel);

        $this->userFormsHelper = $userFormsHelper;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $user = $this->getData();

        return \array_merge(
            $this->userFormsHelper->fetchUserSettingsFormFields(
                $user['address_display'],
                $user['birthday_display'],
                $user['country_display'],
                $user['mail_display']
            ),
            [
                'form' => \array_merge($user, $this->getRequestData()),
                'form_token' => $this->formToken->renderFormToken(),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return [];
    }
}
