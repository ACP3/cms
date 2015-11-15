<?php
namespace ACP3\Modules\ACP3\Seo\Helpers\DataGrid\ColumnRenderer;

use ACP3\Core\Helpers\DataGrid\ColumnRenderer\AbstractColumnRenderer;
use ACP3\Core\Lang;
use ACP3\Core\SEO;

/**
 * Class SeoRobotsColumnRenderer
 * @package ACP3\Modules\ACP3\Seo\Helpers\DataGrid\ColumnRenderer
 */
class SeoRobotsColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\SEO
     */
    protected $seo;
    /**
     * @var array
     */
    protected $search = [0, 1, 2, 3, 4];
    /**
     * @var array
     */
    protected $replace = [];

    /**
     * SeoRobotsColumnRenderer constructor.
     *
     * @param \ACP3\Core\Lang $lang
     * @param \ACP3\Core\SEO  $seo
     */
    public function __construct(
        Lang $lang,
        SEO $seo
    )
    {
        $this->lang = $lang;
        $this->seo = $seo;
    }

    /**
     * @return string[]
     */
    protected function _setReplaceParams()
    {
        return [
            sprintf($this->lang->t('seo', 'robots_use_system_default'), $this->seo->getRobotsSetting()),
            $this->lang->t('seo', 'robots_index_follow'),
            $this->lang->t('seo', 'robots_index_nofollow'),
            $this->lang->t('seo', 'robots_noindex_follow'),
            $this->lang->t('seo', 'robots_noindex_nofollow')
        ];
    }

    /**
     * @inheritdoc
     */
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow, $identifier, $primaryKey)
    {
        $value = $this->getDbFieldValueIfExists($column, $dbResultRow);

        if (empty($this->replace) === true) {
            $this->replace = $this->_setReplaceParams();
        }

        return $this->render($column, str_replace($this->search, $this->replace, $value));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'seo_robots';
    }
}