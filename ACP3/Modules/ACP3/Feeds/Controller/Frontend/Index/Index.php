<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Feeds;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Feeds\Controller\Frontend\Index
 */
class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Feeds\Helper\FeedGenerator
     */
    protected $feedGenerator;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Modules\ACP3\Feeds\Helper\FeedGenerator $feedGenerator
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Feeds\Helper\FeedGenerator $feedGenerator)
    {
        parent::__construct($context);

        $this->feedGenerator = $feedGenerator;
    }

    /**
     * @param string $feed
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($feed)
    {
        if ($this->acl->hasPermission('frontend/' . $feed) === true) {
            $this->setCacheResponseCacheable($this->config->getSettings('system')['cache_lifetime']);

            $module = $this->request->getParameters()->get('feed', '');
            $this->feedGenerator
                ->setTitle($this->config->getSettings('seo')['title'])
                ->setDescription($this->translator->t($module, $module));

            $this->eventDispatcher->dispatch(
                'feeds.events.displayFeed.' . strtolower($feed),
                new Feeds\Event\DisplayFeed($this->feedGenerator)
            );

            $this->setContentType('text/xml');
            return $this->response->setContent($this->feedGenerator->generateFeed());
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
