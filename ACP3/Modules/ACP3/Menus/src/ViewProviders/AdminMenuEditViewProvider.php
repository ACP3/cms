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
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;

    public function __construct(
        FormToken $formTokenHelper,
        RequestInterface $request,
        Title $title
    ) {
        $this->formTokenHelper = $formTokenHelper;
        $this->request = $request;
        $this->title = $title;
    }

    public function __invoke(array $menu): array
    {
        $this->title->setPageTitlePrefix($menu['title']);

        return [
            'form' => \array_merge($menu, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
