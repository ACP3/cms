<?php

namespace ACP3\Modules\ACP3\Feeds\Controller;

use ACP3\Core;
use ACP3\Core\Modules\FrontendController;
use ACP3\Modules\ACP3\Feeds;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Feeds\Controller
 */
class Index extends Core\Modules\FrontendController
{
    /**
     * @var Feeds\Extensions
     */
    protected $feedsExtensions;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext $context
     * @param \ACP3\Modules\ACP3\Feeds\Extensions           $feedsExtensions
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Feeds\Extensions $feedsExtensions)
    {
        parent::__construct($context);

        $this->feedsExtensions = $feedsExtensions;
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

        $this->view->setRenderer('feedgenerator', $config);

        parent::preDispatch();
    }

    public function actionIndex()
    {
        $action = strtolower($this->request->getParameters()->get('feed', '')) . 'Feed';

        if ($this->acl->hasPermission('frontend/' . $this->request->getParameters()->get('feed', '')) === true &&
            method_exists($this->feedsExtensions, $action) === true
        ) {
            $items = $this->feedsExtensions->$action();
            $this->view->assign($items);

            $this->setContentType('text/xml');
            $this->setTemplate($this->config->getSettings('feeds')['feed_type']);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }
}
