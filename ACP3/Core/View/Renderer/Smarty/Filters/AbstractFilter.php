<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Filters;

abstract class AbstractFilter
{
    abstract public function __invoke($tplOutput, \Smarty_Internal_Template $smarty);
}
