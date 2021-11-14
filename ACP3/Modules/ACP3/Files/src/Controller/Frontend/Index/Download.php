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

class Download extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private Core\Date $date,
        private Core\Http\RedirectResponse $redirectResponse,
        private Core\Helpers\StringFormatter $stringFormatter,
        private Files\Repository\FilesRepository $filesRepository
    ) {
        parent::__construct($context);
    }

    /**
     * @throws Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $id): \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        if ($this->filesRepository->resultExists($id, $this->date->getCurrentDateTime()) === true) {
            $file = $this->filesRepository->getOneById($id);

            $path = $this->appPath->getUploadsDir() . 'files/';
            if (is_file($path . $file['file'])) {
                $ext = strrchr($file['file'], '.');
                $filename = $this->stringFormatter->makeStringUrlSafe($file['title']) . $ext;

                $response = new BinaryFileResponse($path . $file['file']);
                $response->setMaxAge(0);
                $response->setContentDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $filename
                );

                return $response;
            }

            if (preg_match('/^([a-z]+):\/\//', $file['file'])) { // External file
                return $this->redirectResponse->toNewPage($file['file']);
            }
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
