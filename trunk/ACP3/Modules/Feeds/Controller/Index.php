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

    public function __construct(
        Core\Context\Frontend $context,
        Core\Config $feedsConfig)
    {
       parent::__construct($context);

        $this->feedsConfig = $feedsConfig;
    }

    public function actionIndex()
    {
        $module = $this->request->feed;
        $action = strtolower($module) . 'Feed';

        $feed = $this->get('feeds.extensions');

        if ($this->modules->hasPermission('frontend/' . $module) === true &&
            method_exists($feed, $action) === true
        ) {
            $settings = $this->feedsConfig->getSettings();

            define('FEED_LINK', 'http://' . htmlentities($_SERVER['HTTP_HOST'], ENT_QUOTES));

            $config = array(
                'feed_image' => $settings['feed_image'],
                'feed_type' => $settings['feed_type'],
                'feed_link' => FEED_LINK . ROOT_DIR,
                'feed_title' => CONFIG_SEO_TITLE,
                'module' => $module,
            );

            Core\View::setRenderer('FeedGenerator', $config);

            $feed->$action();

            $this->setContentType('text/xml');
            $this->setContentTemplate($settings['feed_type']);
            $this->setLayout('');
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

}