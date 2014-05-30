<?php

namespace ACP3\Modules\Files\Controller;

use ACP3\Core;
use ACP3\Modules\Files;


/**
 * Description of FilesFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller
{

    /**
     *
     * @var Files\Model
     */
    protected $model;
    /**
     * @var \ACP3\Modules\Categories\Model
     */
    protected $categoriesModel;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Files\Model($this->db, $this->lang, $this->uri);
        $this->categoriesModel = new \ACP3\Modules\Categories\Model($this->db, $this->lang);
    }

    public function actionIndex()
    {
        if (Core\Modules::isActive('categories') === true) {
            $categories = $this->categoriesModel->getCache('files');
            if (count($categories) > 0) {
                $this->view->assign('categories', $categories);
            }
        }
    }

    public function actionDetails()
    {
        if ($this->model->resultExists((int) $this->uri->id, $this->date->getCurrentDateTime()) === true) {
            $file = $this->model->getCache($this->uri->id);

            if ($this->uri->action === 'download') {
                $path = UPLOADS_DIR . 'files/';
                if (is_file($path . $file['file'])) {
                    // Schönen Dateinamen generieren
                    $ext = strrchr($file['file'], '.');
                    $filename = Core\Functions::makeStringUrlSafe($file['title']) . $ext;

                    header('Content-Type: application/force-download');
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length:' . filesize($path . $file['file']));
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                    readfile($path . $file['file']);
                    exit;
                } elseif (preg_match('/^([a-z]+):\/\//', $file['file'])) {
                    $this->uri->redirect(0, $file['file']); // Externe Datei
                } else {
                    $this->uri->redirect('errors/index/404');
                }
            } else {
                // Brotkrümelspur
                $this->breadcrumb->append($this->lang->t('files', 'files'), 'files')
                    ->append($file['category_title'], 'files/files/cat_' . $file['category_id'])
                    ->append($file['title']);

                $settings = Core\Config::getSettings('files');

                $file['size'] = !empty($file['size']) ? $file['size'] : $this->lang->t('files', 'unknown_filesize');
                $file['date_formatted'] = $this->date->format($file['start'], $settings['dateformat']);
                $file['date_iso'] = $this->date->format($file['start'], 'c');
                $this->view->assign('file', $file);

                if ($settings['comments'] == 1 && $file['comments'] == 1 && Core\Modules::hasPermission('frontend/comments') === true) {
                    $comments = new \ACP3\Modules\Comments\Controller\Index(
                        $this->auth,
                        $this->breadcrumb,
                        $this->date,
                        $this->db,
                        $this->lang,
                        $this->session,
                        $this->uri,
                        $this->view,
                        $this->seo,
                        'files',
                        $this->uri->id
                    );
                    $this->view->assign('comments', $comments->actionIndex());
                }
            }
        } else {
            $this->uri->redirect('errors/index/404');
        }
    }

    public function actionFiles()
    {
        if (Core\Validate::isNumber($this->uri->cat) && $this->categoriesModel->resultExists($this->uri->cat) === true) {
            $category = $this->categoriesModel->getOneById($this->uri->cat);

            $this->breadcrumb
                ->append($this->lang->t('files', 'files'), 'files')
                ->append($category['title']);

            $files = $this->model->getAllByCategoryId($this->uri->cat, $this->date->getCurrentDateTime());
            $c_files = count($files);

            if ($c_files > 0) {
                $settings = Core\Config::getSettings('files');

                for ($i = 0; $i < $c_files; ++$i) {
                    $files[$i]['size'] = !empty($files[$i]['size']) ? $files[$i]['size'] : $this->lang->t('files', 'unknown_filesize');
                    $files[$i]['date_formatted'] = $this->date->format($files[$i]['start'], $settings['dateformat']);
                    $files[$i]['date_iso'] = $this->date->format($files[$i]['start'], 'c');
                }
                $this->view->assign('files', $files);
            }
        } else {
            $this->uri->redirect('errors/index/404');
        }
    }

}