<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

class Mark extends AbstractFunction
{
    public function __construct(private readonly Core\Helpers\Formatter\MarkEntries $markEntries)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty): mixed
    {
        $markAllId = !empty($params['mark_all_id']) ? $params['mark_all_id'] : 'mark-all';

        return $this->markEntries->execute($params['name'], $markAllId);
    }
}
