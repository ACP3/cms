<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\SEO\MetaStatementsServiceInterface;
use ACP3\Modules\ACP3\Gallery;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Details extends AbstractAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Model\Repository\PictureRepository
     */
    private $pictureRepository;
    /**
     * @var \ACP3\Core\SEO\MetaStatementsServiceInterface
     */
    private $metaStatements;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Helper\ThumbnailGenerator
     */
    private $thumbnailGenerator;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Core\Router\RouterInterface $router,
        Gallery\Model\Repository\PictureRepository $pictureRepository,
        Gallery\Helper\ThumbnailGenerator $thumbnailGenerator,
        MetaStatementsServiceInterface $metaStatements
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->pictureRepository = $pictureRepository;
        $this->thumbnailGenerator = $thumbnailGenerator;
        $this->metaStatements = $metaStatements;
        $this->router = $router;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \ACP3\Core\Picture\Exception\PictureGenerateException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id): array
    {
        if ($this->pictureRepository->pictureExists($id, $this->date->getCurrentDateTime()) === true) {
            $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

            $picture = $this->pictureRepository->getOneById($id);

            $this->breadcrumb
                ->append($this->translator->t('gallery', 'gallery'), 'gallery')
                ->append($picture['gallery_title'], 'gallery/index/pics/id_' . $picture['gallery_id']);

            if (!empty($picture['title'])) {
                $this->breadcrumb->append(
                    $picture['title'],
                    $this->request->getQuery()
                );
            } else {
                $this->breadcrumb->append(
                    $this->translator->t('gallery', 'picture_x', ['%picture%' => $picture['pic']]),
                    $this->request->getQuery()
                );
            }

            $this->title->setPageTitlePostfix($picture['gallery_title']);

            $output = $this->thumbnailGenerator->generateThumbnail($picture['file'], '');
            $picture['file'] = $output->getFileWeb();
            $picture['width'] = $output->getWidth();
            $picture['height'] = $output->getHeight();

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
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    protected function setNextPage(int $nextPicture): void
    {
        $this->metaStatements->setNextPage(
            $this->router->route(\sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $nextPicture))
        );
    }

    protected function setPreviousPage(int $previousPicture): void
    {
        $this->metaStatements->setPreviousPage(
            $this->router->route(\sprintf(Gallery\Helpers::URL_KEY_PATTERN_PICTURE, $previousPicture))
        );
    }
}
