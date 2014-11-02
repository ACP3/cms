<?php

namespace ACP3\Modules\Gallery\Controller;

use ACP3\Core;
use ACP3\Modules\Gallery;

/**
 * Class Index
 * @package ACP3\Modules\Gallery\Controller
 */
class Index extends Core\Modules\Controller\Frontend
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var Core\Pagination
     */
    protected $pagination;
    /**
     * @var Gallery\Model
     */
    protected $galleryModel;
    /**
     * @var Gallery\Cache
     */
    protected $galleryCache;
    /**
     * @var Core\Config
     */
    protected $galleryConfig;
    /**
     * @var array
     */
    protected $settings;

    public function __construct(
        Core\Context\Frontend $context,
        Core\Date $date,
        Core\Pagination $pagination,
        Gallery\Model $galleryModel,
        Gallery\Cache $galleryCache,
        Core\Config $galleryConfig)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pagination = $pagination;
        $this->galleryModel = $galleryModel;
        $this->galleryCache = $galleryCache;
        $this->galleryConfig = $galleryConfig;
    }

    public function preDispatch()
    {
        parent::preDispatch();
        
        $this->settings = $this->galleryConfig->getSettings();
    }

    public function actionDetails()
    {
        if ($this->galleryModel->pictureExists((int)$this->request->id, $this->date->getCurrentDateTime()) === true) {
            $picture = $this->galleryModel->getPictureById((int)$this->request->id);

            // Brotkrümelspur
            $this->breadcrumb
                ->append($this->lang->t('gallery', 'gallery'), 'gallery')
                ->append($picture['title'], 'gallery/index/pics/id_' . $picture['gallery_id'])
                ->append($this->lang->t('gallery', 'details'))
                ->setTitlePrefix($picture['title'])
                ->setTitlePostfix(sprintf($this->lang->t('gallery', 'picture_x'), $picture['pic']));

            // Bildabmessungen berechnen
            $picture['width'] = $this->settings['width'];
            $picture['height'] = $this->settings['height'];
            $picInfos = @getimagesize(UPLOADS_DIR . 'gallery/' . $picture['file']);
            if ($picInfos !== false) {
                if ($picInfos[0] > $this->settings['width'] || $picInfos[1] > $this->settings['height']) {
                    if ($picInfos[0] > $picInfos[1]) {
                        $newWidth = $this->settings['width'];
                        $newHeight = intval($picInfos[1] * $newWidth / $picInfos[0]);
                    } else {
                        $newHeight = $this->settings['height'];
                        $newWidth = intval($picInfos[0] * $newHeight / $picInfos[1]);
                    }
                }

                $picture['width'] = isset($newWidth) ? $newWidth : $picInfos[0];
                $picture['height'] = isset($newHeight) ? $newHeight : $picInfos[1];
            }

            $this->view->assign('picture', $picture);

            // Vorheriges Bild
            $previousPicture = $this->galleryModel->getPreviousPictureId($picture['pic'], $picture['gallery_id']);
            if (!empty($previousPicture)) {
                $this->seo->setPreviousPage($this->router->route('gallery/index/details/id_' . $previousPicture));
                $this->view->assign('picture_back', $previousPicture);
            }

            // Nächstes Bild
            $nextPicture = $this->galleryModel->getNextPictureId($picture['pic'], $picture['gallery_id']);
            if (!empty($nextPicture)) {
                $this->seo->setNextPage($this->router->route('gallery/index/details/id_' . $nextPicture));
                $this->view->assign('picture_next', $nextPicture);
            }

            if ($this->settings['overlay'] == 0 && $this->settings['comments'] == 1 && $picture['comments'] == 1 && $this->acl->hasPermission('frontend/comments') === true) {
                $comments = $this->get('comments.controller.frontend.index');
                $comments
                    ->setModule('gallery')
                    ->setEntryId($this->request->id);

                $this->view->assign('comments', $comments->actionIndex());
            }
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionImage()
    {
        $this->setNoOutput(true);

        if ($this->get('core.validator.rules.misc')->isNumber($this->request->id) === true) {
            @set_time_limit(20);
            $picture = $this->galleryModel->getFileById($this->request->id);
            $action = $this->request->action === 'thumb' ? 'thumb' : '';

            $options = array(
                'enable_cache' => CONFIG_CACHE_IMAGES == 1 ? true : false,
                'cache_prefix' => 'gallery_' . $action,
                'max_width' => $this->settings[$action . 'width'],
                'max_height' => $this->settings[$action . 'height'],
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

        $this->pagination->setTotalResults($this->galleryModel->countAll($time));
        $this->pagination->display();

        $this->view->assign('galleries', $this->galleryModel->getAll($time, POS, $this->auth->entries));
        $this->view->assign('dateformat', $this->settings['dateformat']);
    }

    public function actionPics()
    {
        if ($this->galleryModel->galleryExists((int)$this->request->id, $this->date->getCurrentDateTime()) === true) {
            // Brotkrümelspur
            $this->breadcrumb
                ->append($this->lang->t('gallery', 'gallery'), 'gallery')
                ->append($this->galleryModel->getGalleryTitle($this->request->id));

            $this->view->assign('pictures', $this->galleryCache->getCache($this->request->id));
            $this->view->assign('overlay', (int)$this->settings['overlay']);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

}