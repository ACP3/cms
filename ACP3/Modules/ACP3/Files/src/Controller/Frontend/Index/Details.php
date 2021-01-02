<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Files;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\Response;

class Details extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository
     */
    private $filesRepository;
    /**
     * @var \ACP3\Modules\ACP3\Files\ViewProviders\FileDetailsViewProvider
     */
    private $fileDetailsViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Files\Model\Repository\FilesRepository $filesRepository,
        Files\ViewProviders\FileDetailsViewProvider $fileDetailsViewProvider
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->filesRepository = $filesRepository;
        $this->fileDetailsViewProvider = $fileDetailsViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id): Response
    {
        if ($this->filesRepository->resultExists($id, $this->date->getCurrentDateTime()) === true) {
            $response = $this->renderTemplate(null, ($this->fileDetailsViewProvider)($id));
            $this->setCacheResponseCacheable($response, $this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

            return $response;
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
