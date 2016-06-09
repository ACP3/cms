<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Files;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Files\Controller\Widget\Index
 */
class Index extends Core\Controller\WidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model\FilesRepository
     */
    protected $filesRepository;

    /**
     * @param \ACP3\Core\Controller\Context\WidgetContext    $context
     * @param \ACP3\Core\Date                                $date
     * @param \ACP3\Modules\ACP3\Files\Model\FilesRepository $filesRepository
     */
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\Date $date,
        Files\Model\FilesRepository $filesRepository)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->filesRepository = $filesRepository;
    }

    /**
     * @param int $categoryId
     * @param string $template
     *
     * @return array
     */
    public function execute($categoryId = 0, $template = '')
    {
        $this->setCacheResponseCacheable($this->config->getSettings('system')['cache_minify']);

        $settings = $this->config->getSettings('files');

        if (!empty($categoryId)) {
            $categories = $this->filesRepository->getAllByCategoryId((int)$categoryId, $this->date->getCurrentDateTime(), $settings['sidebar']);
        } else {
            $categories = $this->filesRepository->getAll($this->date->getCurrentDateTime(), $settings['sidebar']);
        }

        $this->setTemplate($template !== '' ? $template : 'Files/Widget/index.index.tpl');

        return [
            'sidebar_files' => $categories
        ];
    }
}
