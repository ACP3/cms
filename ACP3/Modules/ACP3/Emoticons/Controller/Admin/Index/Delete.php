<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Emoticons;

/**
 * Class Delete
 * @package ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index
 */
class Delete extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Model\EmoticonRepository
     */
    protected $emoticonRepository;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Cache
     */
    protected $emoticonsCache;

    /**
     * Delete constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext            $context
     * @param \ACP3\Modules\ACP3\Emoticons\Model\EmoticonRepository $emoticonRepository
     * @param \ACP3\Modules\ACP3\Emoticons\Cache                    $emoticonsCache
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Emoticons\Model\EmoticonRepository $emoticonRepository,
        Emoticons\Cache $emoticonsCache)
    {
        parent::__construct($context);

        $this->emoticonRepository = $emoticonRepository;
        $this->emoticonsCache = $emoticonsCache;
    }

    /**
     * @param string $action
     *
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|void
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($action = '')
    {
        return $this->actionHelper->handleDeleteAction(
            $this,
            $action,
            function ($items) {
                $bool = false;

                $upload = new Core\Helpers\Upload($this->appPath, 'emoticons');
                foreach ($items as $item) {
                    if (!empty($item) && $this->emoticonRepository->resultExists($item) === true) {
                        // Datei ebenfalls löschen
                        $file = $this->emoticonRepository->getOneImageById($item);
                        $upload->removeUploadedFile($file);
                        $bool = $this->emoticonRepository->delete($item);
                    }
                }

                $this->emoticonsCache->saveCache();

                Core\Cache\Purge::doPurge($this->appPath->getCacheDir() . 'http');

                return $bool;
            }
        );
    }
}
