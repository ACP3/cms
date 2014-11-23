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
     * @param Core\Context\Frontend $context
     * @param Core\Config $feedsConfig
     * @param Feeds\Extensions $feedsExtensions
     */
    public function __construct(
        Core\Context\Frontend $context,
        Core\Config $feedsConfig,
        Feeds\Extensions $feedsExtensions
    )
    {
        parent::__construct($context);

        $this->feedsConfig = $feedsConfig;
        $this->feedsExtensions = $feedsExtensions;
    }

    public function preDispatch()
    {
        $settings = $this->feedsConfig->getSettings();

        $config = array(
            'feed_image' => $settings['feed_image'],
            'feed_type' => $settings['feed_type'],
            'feed_link' => $this->router->route('', true),
            'feed_title' => $this->systemConfig->getSettings()['seo_title'],
            'module' => $this->request->feed,
        );

        $this->view->setRenderer('feedgenerator', $config);

        parent::preDispatch();
    }

    public function actionIndex()
    {
        $action = strtolower($this->request->feed) . 'Feed';

        if ($this->acl->hasPermission('frontend/' . $this->request->feed) === true &&
            method_exists($this->feedsExtensions, $action) === true
        ) {
            $settings = $this->feedsConfig->getSettings();

            $this->feedsExtensions->$action();

            $this->setContentType('text/xml');
            $this->setTemplate($settings['feed_type']);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

}