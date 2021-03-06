<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

class RedirectMessage extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Helpers\RedirectMessages
     */
    protected $redirectMessages;

    /**
     * @param \ACP3\Core\Helpers\RedirectMessages $redirectMessages
     */
    public function __construct(Core\Helpers\RedirectMessages $redirectMessages)
    {
        $this->redirectMessages = $redirectMessages;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        $smarty->smarty->assign('redirect', $this->redirectMessages->getMessage());

        return $smarty->smarty->fetch('asset:System/Partials/redirect_message.tpl');
    }
}
