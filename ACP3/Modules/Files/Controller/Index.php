<?php

namespace ACP3\Modules\Files\Controller;

use ACP3\Core;
use ACP3\Modules\Categories;
use ACP3\Modules\Files;


/**
 * Class Index
 * @package ACP3\Modules\Files\Controller
 */
class Index extends Core\Modules\Controller\Frontend
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Files\Model
     */
    protected $filesModel;
    /**
     * @var \ACP3\Modules\Categories\Model
     */
    protected $categoriesModel;

    public function __construct(
        Core\Context\Frontend $context,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Files\Model $filesModel,
        Categories\Model $categoriesModel)
    {
       parent::__construct($context);

        $this->date = $date;
        $this->db = $db;
        $this->filesModel = $filesModel;
        $this->categoriesModel = $categoriesModel;
    }

    public function actionIndex()
    {
        if ($this->modules->isActive('categories') === true) {
            $categoriesCache = new Categories\Cache($this->categoriesModel);
            $categories = $categoriesCache->getCache('files');
            if (count($categories) > 0) {
                $this->view->assign('categories', $categories);
            }
        }
    }

    public function actionDetails()
    {
        if ($this->filesModel->resultExists((int) $this->request->id, $this->date->getCurrentDateTime()) === true) {
            $cache = new Files\Cache($this->filesModel);
            $file = $cache->getCache($this->request->id);

            if ($this->request->action === 'download') {
                $path = UPLOADS_DIR . 'files/';
                if (is_file($path . $file['file'])) {
                    $formatter = $this->get('core.helpers.string.formatter');
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
                    ->append($file['category_title'], 'files/files/cat_' . $file['category_id'])
                    ->append($file['title']);

                $config = new Core\Config($this->db, 'files');
                $settings = $config->getSettings();

                $file['size'] = !empty($file['size']) ? $file['size'] : $this->lang->t('files', 'unknown_filesize');
                $file['date_formatted'] = $this->date->format($file['start'], $settings['dateformat']);
                $file['date_iso'] = $this->date->format($file['start'], 'c');
                $this->view->assign('file', $file);

                if ($settings['comments'] == 1 && $file['comments'] == 1 && $this->modules->hasPermission('frontend/comments') === true) {
                    $comments = $this->get('comments.controller.frontend.index');
                    $comments
                        ->setModule('files')
                        ->setEntryId($this->request->id);
                    $this->view->assign('comments', $comments->actionIndex());
                }
            }
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionFiles()
    {
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->cat) && $this->categoriesModel->resultExists($this->request->cat) === true) {
            $category = $this->categoriesModel->getOneById($this->request->cat);

            $this->breadcrumb
                ->append($this->lang->t('files', 'files'), 'files')
                ->append($category['title']);

            $files = $this->filesModel->getAllByCategoryId($this->request->cat, $this->date->getCurrentDateTime());
            $c_files = count($files);

            if ($c_files > 0) {
                $config = new Core\Config($this->db, 'files');
                $settings = $config->getSettings();

                for ($i = 0; $i < $c_files; ++$i) {
                    $files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : $this->lang->t('files', 'unknown_filesize');
                    $files[$i]['date_formatted'] = $this->date->format($files[$i]['start'], $settings['dateformat']);
                    $files[$i]['date_iso'] = $this->date->format($files[$i]['start'], 'c');
                }
                $this->view->assign('files', $files);
            }
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

}