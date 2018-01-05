<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\View\Block\Admin;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\View\Block\AbstractRepositoryAwareFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Users\Helpers\Forms;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class UserAdminFormBlock extends AbstractRepositoryAwareFormBlock
{
    /**
     * @var ACLInterface
     */
    private $acl;
    /**
     * @var Forms
     */
    private $userFormsHelpers;

    /**
     * UserFormBlock constructor.
     * @param FormBlockContext $context
     * @param UserModel $userModel
     * @param ACLInterface $acl
     * @param Forms $userFormsHelpers
     */
    public function __construct(
        FormBlockContext $context,
        UserModel $userModel,
        ACLInterface $acl,
        Forms $userFormsHelpers
    ) {
        parent::__construct($context, $userModel);

        $this->acl = $acl;
        $this->userFormsHelpers = $userFormsHelpers;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $user = $this->getData();

        $this->title->setPageTitlePrefix($user['nickname']);

        $userRoles = $this->acl->getUserRoleIds($user['id']);

        return \array_merge(
            [
                'roles' => $this->fetchUserRoles($userRoles),
                'super_user' => $this->fetchIsSuperUser($user['super_user']),
                'contact' => $this->userFormsHelpers->fetchContactDetails(
                    $user['mail'],
                    $user['website'],
                    $user['icq'],
                    $user['skype']
                ),
                'form' => \array_merge($user, $this->getRequestData()),
                'form_token' => $this->formToken->renderFormToken(),
            ],
            $this->userFormsHelpers->fetchUserSettingsFormFields(
                $user['address_display'],
                $user['birthday_display'],
                $user['country_display'],
                $user['mail_display']
            ),
            $this->userFormsHelpers->fetchUserProfileFormFields(
                $user['birthday'],
                $user['country'],
                $user['gender']
            )
        );
    }

    /**
     * @param array $currentUserRoles
     *
     * @return array
     */
    private function fetchUserRoles(array $currentUserRoles = []): array
    {
        $roles = $this->acl->getAllRoles();

        $availableUserRoles = [];
        foreach ($roles as $role) {
            $availableUserRoles[$role['id']] = \str_repeat('&nbsp;&nbsp;', $role['level']) . $role['name'];
        }

        return $this->forms->choicesGenerator('roles', $availableUserRoles, $currentUserRoles);
    }

    /**
     * @param int $value
     *
     * @return array
     */
    private function fetchIsSuperUser(int $value = 0): array
    {
        return $this->forms->yesNoCheckboxGenerator('super_user', $value);
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return [
            'id' => 0,
            'super_user' => 0,
            'nickname' => '',
            'realname' => '',
            'mail' => '',
            'website' => '',
            'street' => '',
            'house_number' => '',
            'zip' => '',
            'city' => '',
            'icq' => '',
            'skype' => '',
            'birthday' => '',
            'country' => '',
            'gender' => 1,
            'address_display' => 0,
            'birthday_display' => 0,
            'country_display' => 0,
            'mail_display' => 0,
        ];
    }
}
