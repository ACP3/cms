<?php
namespace ACP3\Modules\Articles\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\Articles;

/**
 * Class Index
 * @package ACP3\Modules\Articles\Controller\Sidebar
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\Articles\Cache
     */
    protected $articlesCache;
    /**
     * @var Articles\Model
     */
    protected $articlesModel;

    /**
     * @param \ACP3\Core\Context           $context
     * @param \ACP3\Core\Date              $date
     * @param \ACP3\Modules\Articles\Model $articlesModel
     * @param \ACP3\Modules\Articles\Cache $articlesCache
     */
    public function __construct(
        Core\Context $context,
        Core\Date $date,
        Articles\Model $articlesModel,
        Articles\Cache $articlesCache)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->articlesModel = $articlesModel;
        $this->articlesCache = $articlesCache;
    }

    /**
     * @param string $template
     */
    public function actionIndex($template = '')
    {
        $this->view->assign('sidebar_articles', $this->articlesModel->getAll($this->date->getCurrentDateTime(), 5));

        $this->setTemplate($template !== '' ? $template : 'Articles/Sidebar/index.index.tpl');
    }

    /**
     * @param int $id
     */
    public function actionSingle($id)
    {
        if ($this->articlesModel->resultExists((int)$id, $this->date->getCurrentDateTime()) === true) {
            $this->view->assign('sidebar_article', $this->articlesCache->getCache($id));

            $this->setTemplate('Articles/Sidebar/index.single.tpl');
        }
    }
}