<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Files;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class Download extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Core\Helpers\StringFormatter
     */
    private $stringFormatter;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository
     */
    private $filesRepository;
    /**
     * @var \ACP3\Modules\ACP3\Files\Cache
     */
    private $filesCache;
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Core\Http\RedirectResponse $redirectResponse,
        Core\Helpers\StringFormatter $stringFormatter,
        Files\Model\Repository\FilesRepository $filesRepository,
        Files\Cache $filesCache
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->stringFormatter = $stringFormatter;
        $this->filesRepository = $filesRepository;
        $this->filesCache = $filesCache;
        $this->redirectResponse = $redirectResponse;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id)
    {
        if ($this->filesRepository->resultExists($id, $this->date->getCurrentDateTime()) === true) {
            $file = $this->filesCache->getCache($id);

            $path = $this->appPath->getUploadsDir() . 'files/';
            if (\is_file($path . $file['file'])) {
                $ext = \strrchr($file['file'], '.');
                $filename = $this->stringFormatter->makeStringUrlSafe($file['title']) . $ext;

                $response = new BinaryFileResponse($path . $file['file']);
                $response->setMaxAge(0);
                $response->setContentDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $filename
                );

                return $response;
            }

            if (\preg_match('/^([a-z]+):\/\//', $file['file'])) { // External file
                return $this->redirectResponse->toNewPage($file['file']);
            }
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
