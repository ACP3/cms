<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\ViewProviders;

use ACP3\Core\ACL;
use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Modules\ACP3\Users\Helpers\Forms as UserFormsHelper;

class AdminUserEditViewProvider
{
    public function __construct(private readonly ACL $acl, private readonly FormToken $formTokenHelper, private readonly Forms $formsHelpers, private readonly UserFormsHelper $userFormsHelpers, private readonly RequestInterface $request, private readonly Title $title)
    {
    }

    /**
     * @param array<string, mixed> $userInfo
     *
     * @return array<string, mixed>
     */
    public function __invoke(array $userInfo): array
    {
        $this->title->setPageTitlePrefix($userInfo['nickname']);

        return array_merge(
            $this->userFormsHelpers->fetchUserSettingsFormFields(
                $userInfo['address_display'] ?? 0,
                $userInfo['birthday_display'] ?? 0,
                $userInfo['country_display'] ?? 0,
                $userInfo['mail_display'] ?? 0
            ),
            $this->userFormsHelpers->fetchUserProfileFormFields(
                $userInfo['birthday'] ?? '',
                $userInfo['country'] ?? '',
                $userInfo['gender'] ?? 1
            ),
            [
                'roles' => $this->fetchUserRoles($userInfo),
                'super_user' => $this->formsHelpers->yesNoCheckboxGenerator(
                    'super_user',
                    $userInfo['super_user'] ?? 0
                ),
                'contact' => $this->userFormsHelpers->fetchContactDetails(
                    $userInfo['mail'] ?? '',
                    $userInfo['website'] ?? '',
                    $userInfo['icq'] ?? '',
                    $userInfo['skype'] ?? ''
                ),
                'form' => array_merge($userInfo, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken(),
            ]
        );
    }

    /**
     * @param array<string, mixed> $userInfo
     *
     * @return array<string, mixed>[]
     */
    private function fetchUserRoles(array $userInfo): array
    {
        $currentUserRoles = isset($userInfo['id']) ? $this->acl->getUserRoleIds($userInfo['id']) : [];

        $roles = $this->acl->getAllRoles();

        $availableUserRoles = [];
        foreach ($roles as $role) {
            $availableUserRoles[$role['id']] = str_repeat('&nbsp;&nbsp;', $role['level']) . $role['name'];
        }

        return $this->formsHelpers->choicesGenerator('roles', $availableUserRoles, $currentUserRoles);
    }
}
