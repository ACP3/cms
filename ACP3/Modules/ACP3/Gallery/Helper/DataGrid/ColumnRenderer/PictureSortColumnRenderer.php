<?php
namespace ACP3\Modules\ACP3\Gallery\Helper\DataGrid\ColumnRenderer;

use ACP3\Core\Helpers\DataGrid\ColumnRenderer\AbstractColumnRenderer;
use ACP3\Core\I18n\Translator;
use ACP3\Core\RouterInterface;

/**
 * Class PictureSortColumnRenderer
 * @package ACP3\Modules\ACP3\Gallery\Helper\DataGrid\ColumnRenderer
 */
class PictureSortColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\RouterInterface
     */
    protected $router;

    /**
     * PictureSortColumnRenderer constructor.
     *
     * @param \ACP3\Core\I18n\Translator $translator
     * @param \ACP3\Core\RouterInterface $router
     */
    public function __construct(
        Translator $translator,
        RouterInterface $router
    ) {
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow)
    {
        $dbValue = $this->getValue($column, $dbResultRow);

        $value = '';
        if ($dbResultRow['last'] != $dbValue) {
            $value .= $this->fetchSortDirectionHtml(
                $this->router->route(sprintf($column['custom']['route_sort_down'], $dbResultRow[$this->primaryKey])),
                'down'
            );
        }
        if ($dbResultRow['first'] != $dbValue) {
            $value .= $this->fetchSortDirectionHtml(
                $this->router->route(sprintf($column['custom']['route_sort_up'], $dbResultRow[$this->primaryKey])),
                'up'
            );
        }
        if ($dbResultRow['first'] == $dbResultRow['last']) {
            $value = $this->fetchSortForbiddenHtml();
        }

        $column['attribute']['data-order'] = $dbResultRow['pic'];

        return $this->render($column, $value);
    }

    /**
     * @param string $url
     * @param string $direction
     * @return string
     */
    protected function fetchSortDirectionHtml($url, $direction)
    {
        $html = <<<HTML
<a href="%s" title="%s" data-ajax-form="true" data-ajax-form-loading-text="%s">
    <i class="glyphicon glyphicon-arrow-%s" aria-hidden="true"></i>
</a>
HTML;

        return sprintf(
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
<i class="glyphicon glyphicon-remove-circle text-danger text-danger" aria-hidden="true" title="%s"></i>
HTML;

        return sprintf($html, $this->translator->t('system', 'move_impossible'));
    }
}
