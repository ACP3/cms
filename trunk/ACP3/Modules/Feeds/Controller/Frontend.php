<?php

namespace ACP3\Modules\Feeds\Controller;

use ACP3\Core;
use ACP3\Modules\Feeds;

/**
 * Description of FeedsFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\Modules\Controller
{
    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Core\Lang $lang,
        Core\Session $session,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo)
    {
        parent::__construct($auth, $breadcrumb, $date, $db, $lang, $session, $uri, $view, $seo);
    }

    public function actionList()
    {
        $module = $this->uri->feed;
        $className = "\\ACP3\\Modules\\Feeds\\Extensions";
        $action = strtolower($module) . 'Feed';
        if (Core\Modules::hasPermission($module, 'list') === true &&
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

            $feed = new Feeds\Extensions();
            $feed->$action();

            $this->view->setNoOutput(true);
            $this->view->setContentType('text/xml');
            $this->view->displayTemplate($settings['feed_type']);
        } else {
            $this->uri->redirect('errors/404');
        }
    }

}