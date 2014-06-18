<?php

namespace ACP3\Modules\Feeds\Controller;

use ACP3\Core;
use ACP3\Modules\Feeds;

/**
 * Description of FeedsFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller
{
    public function actionIndex()
    {
        $module = $this->uri->feed;
        $className = "\\ACP3\\Modules\\Feeds\\Extensions";
        $action = strtolower($module) . 'Feed';
        if (Core\Modules::hasPermission('frontend/' . $module) === true &&
            method_exists($className, $action) === true
        ) {
            $settings = Core\Config::getSettings('feeds');

            define('FEED_LINK', 'http://' . htmlentities($_SERVER['HTTP_HOST'], ENT_QUOTES));

            $config = array(
                'feed_image' => $settings['feed_image'],
                'feed_type' => $settings['feed_type'],
                'feed_link' => FEED_LINK . ROOT_DIR,
                'feed_title' => CONFIG_SEO_TITLE,
                'module' => $module,
            );

            Core\View::factory('FeedGenerator', $config);

            $feed = new Feeds\Extensions(
                $this->db,
                $this->date,
                $this->uri,
                $this->view
            );
            $feed->$action();

            $this->setContentType('text/xml');
            $this->setContentTemplate($settings['feed_type']);
            $this->setLayout('');
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

}