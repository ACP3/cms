<?php

namespace ACP3\Modules\Gallery\Controller;

use ACP3\Core;
use ACP3\Modules\Gallery;

/**
 * Class Index
 * @package ACP3\Modules\Gallery\Controller
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Gallery\Model
     */
    protected $galleryModel;

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Lang $lang,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo,
        Core\Modules $modules,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Gallery\Model $galleryModel)
    {
        parent::__construct($auth, $breadcrumb, $lang, $uri, $view, $seo, $modules);

        $this->date = $date;
        $this->db = $db;
        $this->galleryModel = $galleryModel;
    }

    public function actionDetails()
    {
        if ($this->galleryModel->pictureExists((int) $this->uri->id, $this->date->getCurrentDateTime()) === true) {
            $picture = $this->galleryModel->getPictureById((int) $this->uri->id);

            $config = new Core\Config($this->db, 'gallery');
            $settings = $config->getSettings();

            // Brotkrümelspur
            $this->breadcrumb
                ->append($this->lang->t('gallery', 'gallery'), 'gallery')
                ->append($picture['title'], 'gallery/index/pics/id_' . $picture['gallery_id'])
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
            $picture_back = $this->galleryModel->getPreviousPictureId($picture['pic'], $picture['gallery_id']);
            if (!empty($picture_back)) {
                $this->seo->setPreviousPage($this->uri->route('gallery/index/details/id_' . $picture_back));
                $this->view->assign('picture_back', $picture_back);
            }

            // Nächstes Bild
            $picture_next = $this->galleryModel->getNextPictureId($picture['pic'], $picture['gallery_id']);
            if (!empty($picture_next)) {
                $this->seo->setNextPage($this->uri->route('gallery/index/details/id_' . $picture_next));
                $this->view->assign('picture_next', $picture_next);
            }

            if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $picture['comments'] == 1 && $this->modules->hasPermission('frontend/comments') === true) {
                $comments = $this->get('comments.controller.frontend.index');
                $comments
                    ->setModule('gallery')
                    ->setEntryId($this->uri->id);

                $this->view->assign('comments', $comments->actionIndex());
            }
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionImage()
    {
        $this->setNoOutput(true);

        if ($this->get('core.validate')->isNumber($this->uri->id) === true) {
            @set_time_limit(20);
            $picture = $this->galleryModel->getFileById($this->uri->id);
            $action = $this->uri->action === 'thumb' ? 'thumb' : '';

            $config = new Core\Config($this->db, 'gallery');
            $settings = $config->getSettings();
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

    public function actionIndex()
    {
        $time = $this->date->getCurrentDateTime();

        $galleries = $this->galleryModel->getAll($time, POS, $this->auth->entries);
        $c_galleries = count($galleries);

        if ($c_galleries > 0) {
            $pagination = new Core\Pagination(
                $this->auth,
                $this->breadcrumb,
                $this->lang,
                $this->seo,
                $this->uri,
                $this->view,
                $this->galleryModel->countAll($time)
            );
            $pagination->display();

            $config = new Core\Config($this->db, 'gallery');
            $settings = $config->getSettings();

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
        if ($this->galleryModel->galleryExists((int) $this->uri->id, $this->date->getCurrentDateTime()) === true) {
            // Cache der Galerie holen
            $cache = new Gallery\Cache($this->db, $this->galleryModel);
            $pictures = $cache->getCache($this->uri->id);
            $c_pictures = count($pictures);

            $galleryTitle = $this->galleryModel->getGalleryTitle($this->uri->id);

            // Brotkrümelspur
            $this->breadcrumb
                ->append($this->lang->t('gallery', 'gallery'), 'gallery')
                ->append($galleryTitle);

            if ($c_pictures > 0) {
                $config = new Core\Config($this->db, 'gallery');
                $settings = $config->getSettings();

                for ($i = 0; $i < $c_pictures; ++$i) {
                    $pictures[$i]['uri'] = $this->uri->route($settings['overlay'] == 1 ? 'gallery/index/image/id_' . $pictures[$i]['id'] . '/action_normal' : 'gallery/index/details/id_' . $pictures[$i]['id']);
                    $pictures[$i]['description'] = strip_tags($pictures[$i]['description']);
                }

                $this->view->assign('pictures', $pictures);
                $this->view->assign('overlay', (int)$settings['overlay']);
            }
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

}