<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Core\Application\BootstrapCache;


use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\Users\Model\UserModel;
use FOS\HttpCache\UserContext\ContextProvider;
use FOS\HttpCache\UserContext\UserContext;

class IsAuthenticatedProvider implements ContextProvider
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var UserModel
     */
    private $userModel;
    /**
     * @var ACLInterface
     */
    private $acl;

    /**
     * IsAuthenticatedProvider constructor.
     * @param SettingsInterface $settings
     * @param ACLInterface $acl
     * @param UserModel $userModel
     */
    public function __construct(SettingsInterface $settings, ACLInterface $acl, UserModel $userModel)
    {
        $this->settings = $settings;
        $this->userModel = $userModel;
        $this->acl = $acl;
    }

    /**
     * @inheritdoc
     */
    public function updateUserContext(UserContext $context)
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $context->addParameter('security_secret', $settings['security_secret']);
        $context->addParameter('authenticated', $this->userModel->isAuthenticated());
        $context->addParameter('roles', $this->acl->getUserRoleIds($this->userModel->getUserId()));

        if (intval($settings['cache_vary_user']) === 1) {
            $context->addParameter('user_id', $this->userModel->getUserId());
        }
    }
}
