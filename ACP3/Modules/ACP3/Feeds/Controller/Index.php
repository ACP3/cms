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
    /**
     * @var \ACP3\Modules\ACP3\Feeds\Helper\FeedGenerator
     */
    protected $feedGenerator;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext $frontendContext
     * @param \ACP3\Modules\ACP3\Feeds\Helper\FeedGenerator $feedGenerator
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $frontendContext,
        Feeds\Helper\FeedGenerator $feedGenerator)
    {
        parent::__construct($frontendContext);

        $this->feedGenerator = $feedGenerator;
    }


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

        $this->feedGenerator->configure($config);

        parent::preDispatch();
    }

    /**
     * @param string $feed
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionIndex($feed)
    {
        if ($this->acl->hasPermission('frontend/' . $feed)) {
            $this->eventDispatcher->dispatch(
                'feeds.events.displayFeed.' . strtolower($feed),
                new Feeds\Event\DisplayFeed($this->feedGenerator, $feed)
            );

            $this->setContentType('text/xml');
            return $this->response->setContent($this->feedGenerator->generateFeed());
        }

        throw new Core\Exceptions\ResultNotExists();
    }
}
