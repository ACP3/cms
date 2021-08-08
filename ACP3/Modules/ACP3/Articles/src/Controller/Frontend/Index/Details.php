<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\Response;

class Details extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Repository\ArticleRepository
     */
    private $articleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Articles\ViewProviders\ArticlePaginatedViewProvider
     */
    private $articlePaginatedViewProvider;

    public function __construct(
        WidgetContext $context,
        Articles\ViewProviders\ArticlePaginatedViewProvider $articlePaginatedViewProvider,
        Core\Date $date,
        Articles\Repository\ArticleRepository $articleRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->articleRepository = $articleRepository;
        $this->articlePaginatedViewProvider = $articlePaginatedViewProvider;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\Exception
     */
    public function execute(int $id): Response
    {
        if ($this->articleRepository->resultExists($id, $this->date->getCurrentDateTime()) === true) {
            $viewData = ($this->articlePaginatedViewProvider)($id);

            if ($this->articlePaginatedViewProvider->getLayout()) {
                $this->view->setLayout($this->articlePaginatedViewProvider->getLayout());
            }

            $response = $this->renderTemplate(null, $viewData);
            $this->setCacheResponseCacheable($response, $this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

            return $response;
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
