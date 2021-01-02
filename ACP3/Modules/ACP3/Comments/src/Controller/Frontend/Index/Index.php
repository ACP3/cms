<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Pagination\Exception\InvalidPageException;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\Response;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Comments\ViewProviders\CommentListViewProvider
     */
    private $commentListViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Comments\ViewProviders\CommentListViewProvider $commentListViewProvider
    ) {
        parent::__construct($context);

        $this->commentListViewProvider = $commentListViewProvider;
    }

    /**
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(string $module, int $entryId): Response
    {
        try {
            $response = $this->renderTemplate(null, ($this->commentListViewProvider)($module, $entryId));
            $this->setCacheResponseCacheable($response, $this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

            return $response;
        } catch (InvalidPageException $e) {
            throw new ResultNotExistsException();
        }
    }
}
