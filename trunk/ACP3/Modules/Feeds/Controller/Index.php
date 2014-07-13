<?php

namespace ACP3\Modules\Feeds\Controller;

use ACP3\Core;
use ACP3\Modules\Feeds;

/**
 * Class Index
 * @package ACP3\Modules\Feeds\Controller
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Lang $lang,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo,
        Core\Modules $modules,
        \Doctrine\DBAL\Connection $db)
    {
        parent::__construct($auth, $breadcrumb, $lang, $uri, $view, $seo, $modules);

        $this->db = $db;
    }

    public function actionIndex()
    {
        $module = $this->uri->feed;
        $className = "\\ACP3\\Modules\\Feeds\\Extensions";
        $action = strtolower($module) . 'Feed';
        if ($this->modules->hasPermission('frontend/' . $module) === true &&
            method_exists($className, $action) === true
        ) {
            $config = new Core\Config($this->db, 'feeds');
            $settings = $config->getSettings();

            define('FEED_LINK', 'http://' . htmlentities($_SERVER['HTTP_HOST'], ENT_QUOTES));

            $config = array(
                'feed_image' => $settings['feed_image'],
                'feed_type' => $settings['feed_type'],
                'feed_link' => FEED_LINK . ROOT_DIR,
                'feed_title' => CONFIG_SEO_TITLE,
                'module' => $module,
            );

            Core\View::factory('FeedGenerator', $config);

            $feed = $this->get('feeds.extensions');
            $feed->$action();

            $this->setContentType('text/xml');
            $this->setContentTemplate($settings['feed_type']);
            $this->setLayout('');
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

}