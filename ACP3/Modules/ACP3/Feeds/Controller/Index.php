<?php

namespace ACP3\Modules\ACP3\Feeds\Controller;

use ACP3\Core;
use ACP3\Modules\ACP3\Feeds;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Feeds\Controller
 */
class Index extends Core\Modules\FrontendController
{
    public function preDispatch()
    {
        $settings = $this->config->getSettings('feeds');

        $config = [
            'feed_image' => $settings['feed_image'],
            'feed_type' => $settings['feed_type'],
            'feed_link' => $this->router->route('', true),
            'feed_title' => $this->config->getSettings('seo')['title'],
            'module' => $this->request->getParameters()->get('feed', ''),
        ];

        $this->view->setRenderer('feedgenerator', $config);

        parent::preDispatch();
    }

    /**
     * @param string $feed
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionIndex($feed)
    {
        if ($this->acl->hasPermission('frontend/' . $feed)) {
            $this->eventDispatcher->dispatch(
                'feeds.events.displayFeed',
                new Feeds\Event\DisplayFeed($this->view, $feed)
            );

            $this->setContentType('text/xml');
            $this->setTemplate($this->config->getSettings('feeds')['feed_type']);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }
}
