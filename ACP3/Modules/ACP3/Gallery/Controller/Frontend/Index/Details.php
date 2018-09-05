<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Details extends AbstractAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    protected $pictureRepository;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements
     */
    protected $metaStatements;
    /**
     * @var array
     */
    protected $settings = [];
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator
     */
    private $thumbnailGenerator;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        Gallery\Helper\ThumbnailGenerator $thumbnailGenerator
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->pictureRepository = $pictureRepository;
        $this->thumbnailGenerator = $thumbnailGenerator;
    }

    /**
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements $metaStatements
     */
    public function setMetaStatements(MetaStatements $metaStatements)
    {
        $this->metaStatements = $metaStatements;
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     */
    public function execute($id)
    {
        if ($this->pictureRepository->pictureExists($id, $this->date->getCurrentDateTime()) === true) {
            $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

            $picture = $this->pictureRepository->getOneById($id);

            $this->breadcrumb
                ->append($this->translator->t('gallery', 'gallery'), 'gallery')
                ->append($picture['gallery_title'], 'gallery/index/pics/id_' . $picture['gallery_id'])
                ->append($this->translator->t('gallery', 'picture_x', ['%picture%' => $picture['pic']]));

            $this->title->setPageTitlePrefix($picture['gallery_title']);

            /** @var \ACP3\Core\Picture $image */
            $image = $this->get('core.image');
            $this->thumbnailGenerator->generateThumbnail($image, '', $picture['file']);
            $picture['file'] = $image->getFileWeb();

            $picture = \array_merge($picture, $this->calculatePictureDimensions($image->getFile()));

            $previousPicture = $this->pictureRepository->getPreviousPictureId($picture['pic'], $picture['gallery_id']);
            if (!empty($previousPicture)) {
                $this->setPreviousPage((int) $previousPicture);
            }

            $nextPicture = $this->pictureRepository->getNextPictureId($picture['pic'], $picture['gallery_id']);
            if (!empty($nextPicture)) {
                $this->setNextPage((int) $nextPicture);
            }

            return [
                'picture' => $picture,
                'picture_next' => $nextPicture,
                'picture_previous' => $previousPicture,
                'comments_allowed' => $this->isCommentsAllowed($picture),
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    protected function calculatePictureDimensions(string $fileName)
    {
        $dimensions = [
            'width' => $this->settings['width'],
            'height' => $this->settings['height'],
        ];
        $picInfos = @\getimagesize($fileName);
        if ($picInfos !== false) {
            if ($picInfos[0] > $this->settings['width'] || $picInfos[1] > $this->settings['height']) {
                if ($picInfos[0] > $picInfos[1]) {
                    $newWidth = $this->settings['width'];
                    $newHeight = (int) ($picInfos[1] * $newWidth / $picInfos[0]);
                } else {
                    $newHeight = $this->settings['height'];
                    $newWidth = (int) ($picInfos[0] * $newHeight / $picInfos[1]);
                }
            }

            $dimensions['width'] = $newWidth ?? $picInfos[0];
            $dimensions['height'] = $newHeight ?? $picInfos[1];
        }

        return $dimensions;
    }

    /**
     * @param int $nextPicture
     */
    protected function setNextPage($nextPicture)
    {
        if ($this->metaStatements instanceof MetaStatements) {
            $this->metaStatements->setNextPage(
                $this->router->route(\sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $nextPicture))
            );
        }
    }

    /**
     * @param int $previousPicture
     */
    protected function setPreviousPage($previousPicture)
    {
        if ($this->metaStatements instanceof MetaStatements) {
            $this->metaStatements->setPreviousPage(
                $this->router->route(\sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $previousPicture))
            );
        }
    }

    /**
     * @param array $picture
     *
     * @return bool
     */
    protected function isCommentsAllowed(array $picture)
    {
        return $this->settings['overlay'] == 0 && $this->settings['comments'] == 1 && $picture['comments'] == 1;
    }
}
