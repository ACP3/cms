<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Helpers\View\CheckAccess as CheckAccessViewHelper;
use ACP3\Core\View\Renderer\Smarty\Functions\AbstractFunction;

class CheckAccess extends AbstractFunction
{
    public function __construct(private CheckAccessViewHelper $checkAccess)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        $return = $this->checkAccess->outputLinkOrButton($params);

        if (\is_array($return)) {
            $smarty->smarty->assign('access_check', $return);

            return $smarty->smarty->fetch('asset:System/Partials/access_check.tpl');
        }

        return $return;
    }
}
