<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

class DateRange extends AbstractFunction
{
    public function __construct(protected \ACP3\Core\Helpers\Formatter\DateRange $dateRangeFormatter)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty): mixed
    {
        $format = $params['format'] ?? 'long';

        if (isset($params['start'], $params['end'])) {
            return $this->dateRangeFormatter->formatTimeRange($params['start'], $params['end'], $format);
        }

        if (isset($params['start'])) {
            return $this->dateRangeFormatter->formatTimeRange($params['start'], '', $format);
        }

        return '';
    }
}
