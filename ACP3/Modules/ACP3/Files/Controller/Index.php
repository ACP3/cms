<?php

namespace ACP3\Modules\ACP3\Files\Controller;

use ACP3\Core;
use ACP3\Core\Modules\FrontendController;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Files;


/**
 * Class Index
 * @package ACP3\Modules\ACP3\Files\Controller
 */
class Index extends Core\Modules\FrontendController
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model
     */
    protected $filesModel;
    /**
     * @var \ACP3\Modules\ACP3\Files\Cache
     */
    protected $filesCache;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model
     */
    protected $categoriesModel;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext $context
     * @param \ACP3\Core\Date                               $date
     * @param \ACP3\Modules\ACP3\Files\Model                $filesModel
     * @param \ACP3\Modules\ACP3\Files\Cache                $filesCache
     * @param \ACP3\Modules\ACP3\Categories\Model           $categoriesModel
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Date $date,
        Files\Model $filesModel,
        Files\Cache $filesCache,
        Categories\Model $categoriesModel)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->filesModel = $filesModel;
        $this->filesCache = $filesCache;
        $this->categoriesModel = $categoriesModel;
    }

    public function actionIndex()
    {
        if ($this->modules->isActive('categories') === true) {
            $categories = $this->get('categories.cache')->getCache('files');
            if (count($categories) > 0) {
                $this->view->assign('categories', $categories);
            }
        }
    }

    /**
     * @param int $id
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionDetails($id)
    {
        if ($this->filesModel->resultExists($id, $this->date->getCurrentDateTime()) === true) {
            $file = $this->filesCache->getCache($id);

            if ($this->request->getParameters()->get('action') === 'confirmed') {
                $path = UPLOADS_DIR . 'files/';
                if (is_file($path . $file['file'])) {
                    $formatter = $this->get('core.helpers.stringFormatter');
                    // Schönen Dateinamen generieren
                    $ext = strrchr($file['file'], '.');
                    $filename = $formatter->makeStringUrlSafe($file['title']) . $ext;

                    header('Content-Type: application/force-download');
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length:' . filesize($path . $file['file']));
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                    readfile($path . $file['file']);
                    exit;
                } elseif (preg_match('/^([a-z]+):\/\//', $file['file'])) {
                    $this->redirect()->toNewPage($file['file']); // Externe Datei
                } else {
                    throw new Core\Exceptions\ResultNotExists();
                }
            } else {
                // Brotkrümelspur
                $this->breadcrumb
                    ->append($this->lang->t('files', 'files'), 'files')
                    ->append($file['category_title'], 'files/index/files/cat_' . $file['category_id'])
                    ->append($file['title']);

                $settings = $this->config->getSettings('files');

                $this->view->assign('file', $file);
                $this->view->assign('dateformat', $settings['dateformat']);

                if ($settings['comments'] == 1 && $file['comments'] == 1 && $this->acl->hasPermission('frontend/comments') === true) {
                    $comments = $this->get('comments.controller.frontend.index');
                    $comments
                        ->setModule('files')
                        ->setEntryId($this->request->getParameters()->get('id'));
                    $this->view->assign('comments', $comments->actionIndex());
                }
            }
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param int $cat
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionFiles($cat)
    {
        if ($this->get('core.validator.rules.misc')->isNumber($cat) && $this->categoriesModel->resultExists($cat) === true) {
            $category = $this->categoriesModel->getOneById($cat);

            $this->breadcrumb
                ->append($this->lang->t('files', 'files'), 'files')
                ->append($category['title']);

            $settings = $this->config->getSettings('files');

            $this->view->assign('dateformat', $settings['dateformat']);
            $this->view->assign('files', $this->filesModel->getAllByCategoryId($cat, $this->date->getCurrentDateTime()));
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }
}
