<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Helpers\View\LoadModule as LoadModuleViewHelper;

class LoadModule extends AbstractFunction
{
    public function __construct(
        private readonly LoadModuleViewHelper $loadModuleViewHelper,
    ) {
    }

    public function __invoke(array $params, \Smarty_Internal_Template $smarty): string
    {
        return ($this->loadModuleViewHelper)($params['module'], $this->parseControllerActionArguments($params));
    }

    /**
     * @param array<string, mixed> $arguments
     *
     * @return string[]
     */
    private function parseControllerActionArguments(array $arguments): array
    {
        if (isset($arguments['args']) && \is_array($arguments['args'])) {
            return $arguments['args'];
        }

        unset($arguments['module']);

        return $arguments;
    }
}
