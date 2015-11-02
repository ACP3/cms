<?php
namespace ACP3\Core\Helpers\DataTable;

use ACP3\Core\Date;
use ACP3\Core\Helpers\Formatter\DateRange;
use ACP3\Core\Lang;
use ACP3\Core\Router;

/**
 * Class ColumnRenderer
 * @package ACP3\Core\Helpers\DataTable
 */
class ColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\Helpers\Formatter\DateRange
     */
    protected $dateRangeHelper;

    /**
     * @param \ACP3\Core\Date                        $date
     * @param \ACP3\Core\Lang                        $lang
     * @param \ACP3\Core\Router                      $router
     * @param \ACP3\Core\Helpers\Formatter\DateRange $dateRangeHelper
     */
    public function __construct(
        Date $date,
        Lang $lang,
        Router $router,
        DateRange $dateRangeHelper
    )
    {
        $this->date = $date;
        $this->lang = $lang;
        $this->router = $router;
        $this->dateRangeHelper = $dateRangeHelper;
    }

    /**
     * @param string $date
     *
     * @return string
     */
    public function renderDateColumn($date)
    {
        $attribute = [
            'data-order' => $this->date->format($date, 'U')
        ];
        return $this->renderColumn($this->dateRangeHelper->formatTimeRange($date), self::TYPE_TD, $attribute);
    }

    /**
     * @param string $dateStart
     * @param string $dateEnd
     *
     * @return string
     */
    public function renderDateRangeColumn($dateStart, $dateEnd)
    {
        $value = $this->dateRangeHelper->formatTimeRange($dateStart, $dateEnd);
        $attribute = [
            'data-order' => $this->date->format($dateStart, 'U')
        ];
        return $this->renderColumn($value, self::TYPE_TD, $attribute, 'datagrid-column__date-range');
    }

    /**
     * @param int    $id
     * @param string $resourcePathDelete
     * @param string $resourcePathEdit
     * @param bool   $canDelete
     * @param bool   $canEdit
     *
     * @return string
     */
    public function renderActionButtons($id, $resourcePathDelete, $resourcePathEdit, $canDelete, $canEdit)
    {
        $value = '';

        if ($canEdit) {
            $resourcePathEdit .= !preg_match('=/$=', $resourcePathEdit) ? '/' : '';
            $value .= '<a href="' . $this->router->route($resourcePathEdit . 'id_' . $id) . '" class="btn btn-default btn-xs btn-block">';
            $value .= '<i class="glyphicon glyphicon-edit"></i> ' . $this->lang->t('system', 'edit');
            $value .= '</a>';
        }

        if ($canDelete) {
            $resourcePathDelete .= !preg_match('=/$=', $resourcePathDelete) ? '/' : '';
            $value .= ' <a href="' . $this->router->route($resourcePathDelete . 'entries_' . $id) . '" class="btn btn-danger btn-xs btn-block">';
            $value .= '<i class="glyphicon glyphicon-remove"></i> ' . $this->lang->t('system', 'delete');
            $value .= '</a>';
        }

        return $this->renderColumn($value);
    }

    /**
     * @param int  $id
     * @param bool $canDelete
     *
     * @return string
     */
    public function renderDeleteCheckbox($id, $canDelete)
    {
        if ($canDelete === true) {
            $value = '<input type="checkbox" name="entries[]" value="' . $id . '">';
            return $this->renderColumn($value);
        }

        return '';
    }
}