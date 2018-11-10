<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\Date;

class DateFormat extends AbstractFunction
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;

    /**
     * @param \ACP3\Core\Date $date
     */
    public function __construct(Date $date)
    {
        $this->date = $date;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty)
    {
        $format = $params['format'] ?? 'long';

        if (isset($params['date'])) {
            return $this->date->format($params['date'], $format);
        }

        return '';
    }
}
