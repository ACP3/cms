<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Files;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


/**
 * Class Details
 * @package ACP3\Modules\ACP3\Files\Controller\Frontend\Index
 */
class Details extends Core\Controller\FrontendController
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model\FilesRepository
     */
    protected $filesRepository;
    /**
     * @var \ACP3\Modules\ACP3\Files\Cache
     */
    protected $filesCache;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext  $context
     * @param \ACP3\Core\Date                                $date
     * @param \ACP3\Modules\ACP3\Files\Model\FilesRepository $filesRepository
     * @param \ACP3\Modules\ACP3\Files\Cache                 $filesCache
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Files\Model\FilesRepository $filesRepository,
        Files\Cache $filesCache)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->filesRepository = $filesRepository;
        $this->filesCache = $filesCache;
    }

    /**
     * @param int    $id
     * @param string $action
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id, $action = '')
    {
        if ($this->filesRepository->resultExists($id, $this->date->getCurrentDateTime()) === true) {
            $file = $this->filesCache->getCache($id);

            if ($action === 'download') {
                return $this->downloadFile($file);
            }

            $this->breadcrumb
                ->append($this->translator->t('files', 'files'), 'files')
                ->append($file['category_title'], 'files/index/files/cat_' . $file['category_id'])
                ->append($file['title']);

            $settings = $this->config->getSettings('files');

            return [
                'file' => $file,
                'dateformat' => $settings['dateformat'],
                'comments_allowed' => $settings['comments'] == 1 && $file['comments'] == 1
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array $file
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    protected function downloadFile(array $file)
    {
        $path = $this->appPath->getUploadsDir() . 'files/';
        if (is_file($path . $file['file'])) {
            $formatter = $this->get('core.helpers.stringFormatter');

            $ext = strrchr($file['file'], '.');
            $filename = $formatter->makeStringUrlSafe($file['title']) . $ext;

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

        throw new Core\Exceptions\ResultNotExists();
    }
}
