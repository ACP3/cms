<?php

namespace ACP3\Modules\Gallery\Controller;

use ACP3\Core;
use ACP3\Modules\Gallery;

/**
 * Description of GalleryFrontend
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

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Core\Lang $lang,
        Core\Session $session,
        Core\URI $uri,
        Core\View $view)
    {
        parent::__construct($auth, $breadcrumb, $date, $db, $lang, $session, $uri, $view);

        $this->model = new Gallery\Model($this->db);
    }

    public function actionDetails()
    {
        if ($this->model->pictureExists((int) $this->uri->id, $this->date->getCurrentDateTime()) === true) {
            $picture = $this->model->getPictureById((int) $this->uri->id);

            $settings = Core\Config::getSettings('gallery');

            // Brotkrümelspur
            $this->breadcrumb
                ->append($this->lang->t('gallery', 'gallery'), $this->uri->route('gallery'))
                ->append($picture['title'], $this->uri->route('gallery/pics/id_' . $picture['gallery_id']))
                ->append($this->lang->t('gallery', 'details'))
                ->setTitlePrefix($picture['title'])
                ->setTitlePostfix(sprintf($this->lang->t('gallery', 'picture_x'), $picture['pic']));

            // Bildabmessungen berechnen
            $picture['width'] = $settings['width'];
            $picture['height'] = $settings['height'];
            $picInfos = @getimagesize(UPLOADS_DIR . 'gallery/' . $picture['file']);
            if ($picInfos !== false) {
                if ($picInfos[0] > $settings['width'] || $picInfos[1] > $settings['height']) {
                    if ($picInfos[0] > $picInfos[1]) {
                        $newWidth = $settings['width'];
                        $newHeight = intval($picInfos[1] * $newWidth / $picInfos[0]);
                    } else {
                        $newHeight = $settings['height'];
                        $newWidth = intval($picInfos[0] * $newHeight / $picInfos[1]);
                    }
                }

                $picture['width'] = isset($newWidth) ? $newWidth : $picInfos[0];
                $picture['height'] = isset($newHeight) ? $newHeight : $picInfos[1];
            }

            $this->view->assign('picture', $picture);

            // Vorheriges Bild
            $picture_back = $this->model->getPreviousPictureId($picture['pic'], $picture['gallery_id']);
            if (!empty($picture_back)) {
                Core\SEO::setPreviousPage($this->uri->route('gallery/details/id_' . $picture_back));
                $this->view->assign('picture_back', $picture_back);
            }

            // Nächstes Bild
            $picture_next = $this->model->getNextPictureId($picture['pic'], $picture['gallery_id']);
            if (!empty($picture_next)) {
                Core\SEO::setNextPage($this->uri->route('gallery/details/id_' . $picture_next));
                $this->view->assign('picture_next', $picture_next);
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $picture['comments'] == 1 && Core\Modules::hasPermission('comments', 'list') === true) {
                $comments = new \ACP3\Modules\Comments\Controller\Frontend(
                    $this->auth,
                    $this->breadcrumb,
                    $this->date,
                    $this->db,
                    $this->lang,
                    $this->session,
                    $this->uri,
                    $this->view,
                    'gallery',
                    $this->uri->id
                );
                $this->view->assign('comments', $comments->actionList());
            }
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionImage()
    {
        $this->view->setNoOutput(true);

        if (Core\Validate::isNumber($this->uri->id) === true) {
            @set_time_limit(20);
            $picture = $this->model->getFileById($this->uri->id);
            $action = $this->uri->action === 'thumb' ? 'thumb' : '';

            $settings = Core\Config::getSettings('gallery');
            $options = array(
                'enable_cache' => CONFIG_CACHE_IMAGES == 1 ? true : false,
                'cache_prefix' => 'gallery_' . $action,
                'max_width' => $settings[$action . 'width'],
                'max_height' => $settings[$action . 'height'],
                'file' => UPLOADS_DIR . 'gallery/' . $picture,
                'prefer_height' => $action === 'thumb' ? true : false
            );

            $image = new Core\Image($options);
            $image->output();
        }
    }

    public function actionList()
    {
        $time = $this->date->getCurrentDateTime();

        $galleries = $this->model->getAll($time, POS, $this->auth->entries);
        $c_galleries = count($galleries);

        if ($c_galleries > 0) {
            $this->view->assign('pagination', Core\Functions::pagination($this->model->countAll($time)));

            $settings = Core\Config::getSettings('gallery');

            for ($i = 0; $i < $c_galleries; ++$i) {
                $galleries[$i]['date_formatted'] = $this->date->format($galleries[$i]['start'], $settings['dateformat']);
                $galleries[$i]['date_iso'] = $this->date->format($galleries[$i]['start'], 'c');
                $galleries[$i]['pics_lang'] = $galleries[$i]['pics'] . ' ' . $this->lang->t('gallery', $galleries[$i]['pics'] == 1 ? 'picture' : 'pictures');
            }
            $this->view->assign('galleries', $galleries);
        }
    }

    public function actionPics()
    {
        if ($this->model->galleryExists((int) $this->uri->id, $this->date->getCurrentDateTime()) === true) {
            // Cache der Galerie holen
            $pictures = $this->model->getCache($this->uri->id);
            $c_pictures = count($pictures);

            if ($c_pictures > 0) {
                $galleryTitle = $this->model->getGalleryTitle($this->uri->id);

                // Brotkrümelspur
                $this->breadcrumb
                    ->append($this->lang->t('gallery', 'gallery'), $this->uri->route('gallery'))
                    ->append($galleryTitle);

                $settings = Core\Config::getSettings('gallery');

                for ($i = 0; $i < $c_pictures; ++$i) {
                    $pictures[$i]['uri'] = $this->uri->route($settings['overlay'] == 1 ? 'gallery/image/id_' . $pictures[$i]['id'] . '/action_normal' : 'gallery/details/id_' . $pictures[$i]['id']);
                    $pictures[$i]['description'] = strip_tags($pictures[$i]['description']);
                }

                $this->view->assign('pictures', $pictures);
                $this->view->assign('overlay', (int)$settings['overlay']);
            }
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionSidebar()
    {
        $settings = Core\Config::getSettings('gallery');

        $galleries = $this->model->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        $c_galleries = count($galleries);

        if ($c_galleries > 0) {
            for ($i = 0; $i < $c_galleries; ++$i) {
                $galleries[$i]['start'] = $this->date->format($galleries[$i]['start']);
                $galleries[$i]['title_short'] = Core\Functions::shortenEntry($galleries[$i]['title'], 30, 5, '...');
            }
            $this->view->assign('sidebar_galleries', $galleries);
        }

        $this->view->displayTemplate('gallery/sidebar.tpl');
    }

}