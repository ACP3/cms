<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\ViewProviders;

use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;

class CommentCreateViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;

    public function __construct(FormToken $formTokenHelper, RequestInterface $request, UserModelInterface $user)
    {
        $this->formTokenHelper = $formTokenHelper;
        $this->request = $request;
        $this->user = $user;
    }

    public function __invoke(string $module, int $entryId, string $redirectUrl): array
    {
        return [
            'form' => \array_merge($this->fetchFormDefaults(), $this->request->getPost()->all()),
            'module' => $module,
            'entry_id' => $entryId,
            'redirect_url' => $redirectUrl,
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    private function fetchFormDefaults(): array
    {
        $defaults = [
            'name' => '',
            'name_disabled' => false,
            'message' => '',
        ];

        if ($this->user->isAuthenticated() === true) {
            $user = $this->user->getUserInfo();
            $defaults['name'] = $user['nickname'];
            $defaults['name_disabled'] = true;
            $defaults['message'] = '';
        }

        return $defaults;
    }
}
