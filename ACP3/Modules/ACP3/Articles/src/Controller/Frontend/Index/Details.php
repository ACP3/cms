<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Details extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository
     */
    private $articleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Articles\ViewProviders\ArticlePaginatedViewProvider
     */
    private $articlePaginatedViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Articles\ViewProviders\ArticlePaginatedViewProvider $articlePaginatedViewProvider,
        Core\Date $date,
        Articles\Model\Repository\ArticleRepository $articleRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->articleRepository = $articleRepository;
        $this->articlePaginatedViewProvider = $articlePaginatedViewProvider;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id): array
    {
        if ($this->articleRepository->resultExists($id, $this->date->getCurrentDateTime()) === true) {
            $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

            $viewData = ($this->articlePaginatedViewProvider)($id);

            if ($this->articlePaginatedViewProvider->getLayout()) {
                $this->setLayout($this->articlePaginatedViewProvider->getLayout());
            }

            return $viewData;
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
