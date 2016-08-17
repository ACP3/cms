<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Files;
use ACP3\Modules\ACP3\System\Installer\Schema;

/**
 * Class Details
 * @package ACP3\Modules\ACP3\Files\Controller\Frontend\Index
 */
class Details extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository
     */
    protected $filesRepository;
    /**
     * @var \ACP3\Modules\ACP3\Files\Cache
     */
    protected $filesCache;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Core\Date $date
     * @param \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository $filesRepository
     * @param \ACP3\Modules\ACP3\Files\Cache $filesCache
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Files\Model\Repository\FilesRepository $filesRepository,
        Files\Cache $filesCache
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->filesRepository = $filesRepository;
        $this->filesCache = $filesCache;
    }

    /**
     * @param int $id
     * @return array
     * @throws Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        if ($this->filesRepository->resultExists($id, $this->date->getCurrentDateTime()) === true) {
            $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

            $file = $this->filesCache->getCache($id);

            $this->breadcrumb
                ->append($this->translator->t('files', 'files'), 'files')
                ->append($file['category_title'], 'files/index/files/cat_' . $file['category_id'])
                ->append($file['title']);

            $settings = $this->config->getSettings(Files\Installer\Schema::MODULE_NAME);
            $file['text'] = $this->view->fetchStringAsTemplate($file['text']);

            return [
                'file' => $file,
                'dateformat' => $settings['dateformat'],
                'comments_allowed' => $settings['comments'] == 1 && $file['comments'] == 1
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
