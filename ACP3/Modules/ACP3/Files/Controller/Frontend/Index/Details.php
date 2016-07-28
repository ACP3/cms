<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Files;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
     * @var \ACP3\Core\Helpers\StringFormatter
     */
    protected $stringFormatter;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository
     */
    protected $filesRepository;
    /**
     * @var \ACP3\Modules\ACP3\Files\Cache
     */
    protected $filesCache;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext  $context
     * @param \ACP3\Core\Date                                $date
     * @param \ACP3\Core\Helpers\StringFormatter             $stringFormatter
     * @param \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository $filesRepository
     * @param \ACP3\Modules\ACP3\Files\Cache                 $filesCache
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Core\Helpers\StringFormatter $stringFormatter,
        Files\Model\Repository\FilesRepository $filesRepository,
        Files\Cache $filesCache)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->stringFormatter = $stringFormatter;
        $this->filesRepository = $filesRepository;
        $this->filesCache = $filesCache;
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id, $action = '')
    {
        if ($this->filesRepository->resultExists($id, $this->date->getCurrentDateTime()) === true) {
            $this->setCacheResponseCacheable($this->config->getSettings('system')['cache_lifetime']);

            $file = $this->filesCache->getCache($id);

            if ($action === 'download') {
                return $this->downloadFile($file);
            }

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

    /**
     * @param array $file
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws Core\Controller\Exception\ResultNotExistsException
     */
    protected function downloadFile(array $file)
    {
        $path = $this->appPath->getUploadsDir() . 'files/';
        if (is_file($path . $file['file'])) {
            $ext = strrchr($file['file'], '.');
            $filename = $this->stringFormatter->makeStringUrlSafe($file['title']) . $ext;

            $disposition = $this->response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
            $this->setContentType('application/force-download');
            $this->response->headers->add([
                'Content-Disposition' => $disposition,
                'Content-Transfer-Encoding' => 'binary',
                'Content-Length' => filesize($path . $file['file'])
            ]);

            return $this->response->setContent(file_get_contents($path . $file['file']));
        } elseif (preg_match('/^([a-z]+):\/\//', $file['file'])) { // External file
            return $this->redirect()->toNewPage($file['file']);
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
