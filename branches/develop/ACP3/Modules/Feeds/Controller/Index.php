<?php

namespace ACP3\Modules\Feeds\Controller;

use ACP3\Core;
use ACP3\Modules\Feeds;

/**
 * Class Index
 * @package ACP3\Modules\Feeds\Controller
 */
class Index extends Core\Modules\Controller\Frontend
{
    /**
     * @var \ACP3\Core\Config
     */
    protected $feedsConfig;
    /**
     * @var Feeds\Extensions
     */
    protected $feedsExtensions;

    /**
     * @param \ACP3\Core\Context\Frontend    $context
     * @param \ACP3\Core\Config              $feedsConfig
     * @param \ACP3\Modules\Feeds\Extensions $feedsExtensions
     * @param \ACP3\Core\Config              $seoConfig
     */
    public function __construct(
        Core\Context\Frontend $context,
        Core\Config $feedsConfig,
        Feeds\Extensions $feedsExtensions,
        Core\Config $seoConfig) {
        parent::__construct($context);

        $this->feedsConfig = $feedsConfig;
        $this->feedsExtensions = $feedsExtensions;
        $this->seoConfig = $seoConfig;
    }

    public function preDispatch()
    {
        $settings = $this->feedsConfig->getSettings();

        $config = [
            'feed_image' => $settings['feed_image'],
            'feed_type' => $settings['feed_type'],
            'feed_link' => $this->router->route('', true),
            'feed_title' => $this->seoConfig->getSettings()['title'],
            'module' => $this->request->feed,
        ];

        $this->view->setRenderer('feedgenerator', $config);

        parent::preDispatch();
    }

    public function actionIndex()
    {
        $action = strtolower($this->request->feed) . 'Feed';

        if ($this->acl->hasPermission('frontend/' . $this->request->feed) === true &&
            method_exists($this->feedsExtensions, $action) === true
        ) {
            $items = $this->feedsExtensions->$action();
            $this->view->assign($items);

            $this->setContentType('text/xml');
            $this->setTemplate($this->feedsConfig->getSettings()['feed_type']);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }
}
