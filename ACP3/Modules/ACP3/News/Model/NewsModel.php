<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Model;


use ACP3\Core\Config;
use ACP3\Core\Date;
use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Modules\ACP3\News\Installer\Schema;
use ACP3\Modules\ACP3\News\Model\Repository\NewsRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NewsModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @var Date
     */
    protected $date;
    /**
     * @var Secure
     */
    protected $secure;
    /**
     * @var NewsRepository
     */
    protected $newsRepository;
    /**
     * @var Config
     */
    protected $config;

    /**
     * NewsModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param Config $config
     * @param Date $date
     * @param Secure $secure
     * @param NewsRepository $newsRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Config $config,
        Date $date,
        Secure $secure,
        NewsRepository $newsRepository
    ) {
        parent::__construct($eventDispatcher);

        $this->config = $config;
        $this->date = $date;
        $this->secure = $secure;
        $this->newsRepository = $newsRepository;
    }

    /**
     * @param array $formData
     * @param int $userId
     * @param int|null $newsId
     * @return bool|int
     */
    public function saveNews(array $formData, $userId, $newsId = null)
    {
        $settings = $this->config->getSettings(Schema::MODULE_NAME);

        $data = [
            'start' => $this->date->toSQL($formData['start']),
            'end' => $this->date->toSQL($formData['end']),
            'title' => $this->secure->strEncode($formData['title']),
            'text' => $this->secure->strEncode($formData['text'], true),
            'readmore' => $this->useReadMore($formData, $settings),
            'comments' => $this->useComments($formData, $settings),
            'category_id' => (int)$formData['cat'],
            'uri' => $this->secure->strEncode($formData['uri'], true),
            'target' => (int)$formData['target'],
            'link_title' => $this->secure->strEncode($formData['link_title']),
            'user_id' => (int)$userId,
        ];

        return $this->save($this->newsRepository, $data, $newsId);
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return int
     */
    protected function useReadMore(array $formData, array $settings)
    {
        return $settings['readmore'] == 1 && isset($formData['readmore']) ? 1 : 0;
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return int
     */
    protected function useComments(array $formData, array $settings)
    {
        return $settings['comments'] == 1 && isset($formData['comments']) ? 1 : 0;
    }
}
