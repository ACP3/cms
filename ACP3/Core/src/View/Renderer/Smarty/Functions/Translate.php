<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

class Translate extends AbstractFunction
{
    public function __construct(protected Core\I18n\Translator $translator)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty): mixed
    {
        $values = explode('|', (string) $params['t']);
        $params = isset($params['args']) && \is_array($params['args']) ? $params['args'] : [];

        return $this->translator->t($values[0], $values[1], $params);
    }
}
