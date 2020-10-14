<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\ColumnRenderer;

use ACP3\Core\I18n\Translator;
use ACP3\Core\Router\RouterInterface;

class SortColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    protected $router;

    /**
     * PictureSortColumnRenderer constructor.
     */
    public function __construct(
        Translator $translator,
        RouterInterface $router
    ) {
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        $dbValue = $this->getValue($column, $dbResultRow);

        $value = '';
        if ($dbResultRow['last'] != $dbValue) {
            $value .= $this->fetchSortDirectionHtml(
                $this->router->route(\sprintf($column['custom']['route_sort_down'], $dbResultRow[$this->getPrimaryKey()])),
                'down'
            );
        }
        if ($dbResultRow['first'] != $dbValue) {
            $value .= $this->fetchSortDirectionHtml(
                $this->router->route(\sprintf($column['custom']['route_sort_up'], $dbResultRow[$this->getPrimaryKey()])),
                'up'
            );
        }
        if ($dbResultRow['first'] == $dbResultRow['last']) {
            $value = $this->fetchSortForbiddenHtml();
        }

        $column['attribute'] += [
            'sort' => \str_pad(
                $dbResultRow[$this->getFirstDbField($column)],
                \strlen($this->getTotalResults()),
                '0',
                STR_PAD_LEFT
            ),
        ];

        return $this->render($column, $value);
    }

    /**
     * @return string
     */
    protected function fetchSortDirectionHtml(string $url, string $direction)
    {
        $html = <<<HTML
<a href="%s" title="%s" data-ajax-form="true" data-ajax-form-loading-text="%s">
    <i class="fas fa-arrow-%s" aria-hidden="true"></i>
</a>
HTML;

        return \sprintf(
            $html,
            $url,
            $this->translator->t('system', 'move_' . $direction),
            $this->translator->t('system', 'loading_please_wait'),
            $direction
        );
    }

    /**
     * @return string
     */
    protected function fetchSortForbiddenHtml()
    {
        $html = <<<HTML
<i class="fas fa-times-circle text-danger text-danger" aria-hidden="true" title="%s"></i>
HTML;

        return \sprintf($html, $this->translator->t('system', 'move_impossible'));
    }

    public static function mandatoryAttributes(): array
    {
        return ['sort', '_'];
    }
}
