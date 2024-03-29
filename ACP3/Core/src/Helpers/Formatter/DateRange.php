<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\Formatter;

use ACP3\Core\Date;
use ACP3\Core\I18n\Translator;

class DateRange
{
    public function __construct(protected Date $date, protected Translator $translator)
    {
    }

    /**
     * Formats a given single date or date range into the desired format.
     *
     * @param string $start
     * @param string $end
     * @param string $format
     *
     * @return string
     */
    public function formatTimeRange($start, $end = '', $format = 'long')
    {
        if (empty($end) || $start >= $end) {
            if (empty($end)) {
                $title = $this->date->format($start, $format);
            } else {
                $title = $this->translator->t(
                    'system',
                    'date_published_since',
                    ['%date%' => $this->date->format($start, $format)]
                );
            }

            return $this->generateTimeTag($start, $format, $title);
        }
        $dateRange = $this->generateTimeTag($start, $format);
        $dateRange .= '&ndash;';

        return $dateRange . $this->generateTimeTag($end, $format);
    }

    /**
     * @param string $date
     * @param string $format
     * @param string $title
     *
     * @return string
     */
    protected function generateTimeTag($date, $format, $title = '')
    {
        $rfcDate = $this->date->format($date, 'c');
        $title = !empty($title) ? ' title="' . $title . '"' : '';

        return '<time datetime="' . $rfcDate . '"' . $title . '>' . $this->date->format($date, $format) . '</time>';
    }
}
