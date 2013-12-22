<?php

namespace ACP3\Modules\Files\Controller;

use ACP3\Core;
use ACP3\Modules\Files;


/**
 * Description of FilesFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\Modules\Controller
{

    /**
     *
     * @var Model
     */
    protected $model;

    protected $categoriesModel;

    public function __construct(
        \ACP3\Core\Auth $auth,
        \ACP3\Core\Breadcrumb $breadcrumb,
        \ACP3\Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        \ACP3\Core\Lang $lang,
        \ACP3\Core\Session $session,
        \ACP3\Core\URI $uri,
        \ACP3\Core\View $view)
    {
        parent::__construct($auth, $breadcrumb, $date, $db, $lang, $session, $uri, $view);

        $this->model = new Files\Model($this->db);

        $this->categoriesModel = new \ACP3\Modules\Categories\Model($this->db);
    }

    public function actionList()
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
                    $this->uri->redirect('errors/404');
                }
            } else {
                // Brotkrümelspur
                $this->breadcrumb->append($this->lang->t('files', 'files'), $this->uri->route('files'))
                    ->append($file['category_title'], $this->uri->route('files/files/cat_' . $file['category_id']))
                    ->append($file['title']);

                $settings = Core\Config::getSettings('files');

                $file['size'] = !empty($file['size']) ? $file['size'] : $this->lang->t('files', 'unknown_filesize');
                $file['date_formatted'] = $this->date->format($file['start'], $settings['dateformat']);
                $file['date_iso'] = $this->date->format($file['start'], 'c');
                $this->view->assign('file', $file);

                if ($settings['comments'] == 1 && $file['comments'] == 1 && Core\Modules::hasPermission('comments', 'list') === true) {
                    $comments = new \ACP3\Modules\Comments\Controller\Frontend(
                        $this->auth,
                        $this->breadcrumb,
                        $this->date,
                        $this->db,
                        $this->lang,
                        $this->session,
                        $this->uri,
                        $this->view,
                        'files',
                        $this->uri->id
                    );
                    $this->view->assign('comments', $comments->actionList());
                }
            }
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionFiles()
    {
        if (Core\Validate::isNumber($this->uri->cat) && $this->categoriesModel->resultExists($this->uri->cat) === true) {
            $category = $this->categoriesModel->getOneById($this->uri->cat);

            $this->breadcrumb
                ->append($this->lang->t('files', 'files'), $this->uri->route('files'))
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
            $this->uri->redirect('errors/404');
        }
    }

    public function actionSidebar()
    {
        $settings = Core\Config::getSettings('files');

        $files = $this->model->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        $c_files = count($files);

        if ($c_files > 0) {
            for ($i = 0; $i < $c_files; ++$i) {
                $files[$i]['start'] = $this->date->format($files[$i]['start']);
                $files[$i]['title_short'] = Core\Functions::shortenEntry($files[$i]['title'], 30, 5, '...');
            }
            $this->view->assign('sidebar_files', $files);
        }

        $this->view->displayTemplate('files/sidebar.tpl');
    }

}