<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\Response;

class Single extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\Articles\Model\Repository\ArticleRepository
     */
    private $articleRepository;
    /**
     * @var \ACP3\Modules\ACP3\Articles\ViewProviders\ArticleFullViewProvider
     */
    private $articleFullViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\Date $date,
        Articles\Model\Repository\ArticleRepository $articleRepository,
        Articles\ViewProviders\ArticleFullViewProvider $articleFullViewProvider
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->articleRepository = $articleRepository;
        $this->articleFullViewProvider = $articleFullViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id): ?Response
    {
        if ($this->articleRepository->resultExists($id, $this->date->getCurrentDateTime()) === true) {
            $response = $this->renderTemplate(null, ($this->articleFullViewProvider)($id));
            $this->setCacheResponseCacheable($response, $this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

            return $response;
        }

        return null;
    }
}
