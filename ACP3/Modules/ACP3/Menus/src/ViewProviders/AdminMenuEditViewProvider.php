<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\ViewProviders;

use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;

class AdminMenuEditViewProvider
{
    public function __construct(private readonly FormToken $formTokenHelper, private readonly RequestInterface $request, private readonly Title $title)
    {
    }

    /**
     * @param array<string, mixed> $menu
     *
     * @return array<string, mixed>
     */
    public function __invoke(array $menu): array
    {
        $this->title->setPageTitlePrefix($menu['title']);

        return [
            'form' => array_merge($menu, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
