<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\View\Renderer\Smarty\Functions\AbstractFunction;

class HasMultipleOptions extends AbstractFunction
{
    public function __invoke(array $params, \Smarty_Internal_Template $smarty): bool
    {
        /** @var array<string, mixed>[] $haystack */
        $haystack = $params['haystack'];
        /** @var string $needle */
        $needle = $params['needle'];

        $found = 0;
        foreach ($haystack as $row) {
            if ($row['name'] === $needle) {
                ++$found;
            }
        }

        return $found > 1;
    }
}
