<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Feeds;
use ACP3\Modules\ACP3\System\Installer\Schema;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Feeds\Controller\Frontend\Index
 */
class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Feeds\View\Renderer\FeedGenerator
     */
    protected $feedGenerator;
    /**
     * @var Feeds\Utility\FeedAvailabilityRegistrar
     */
    protected $availableFeedsRegistrar;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param \ACP3\Modules\ACP3\Feeds\View\Renderer\FeedGenerator $feedGenerator
     * @param Feeds\Utility\FeedAvailabilityRegistrar $availableFeedsRegistrar
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Feeds\View\Renderer\FeedGenerator $feedGenerator,
        Feeds\Utility\FeedAvailabilityRegistrar $availableFeedsRegistrar
    ) {
        parent::__construct($context);

        $this->feedGenerator = $feedGenerator;
        $this->availableFeedsRegistrar = $availableFeedsRegistrar;
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
            $this->setCacheResponseCacheable(
                $this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']
            );

            try {
                $feedItems = $this->availableFeedsRegistrar
                    ->getFeedItemsByModuleName($feed)
                    ->fetchFeedItems();

                $this->feedGenerator
                    ->setTitle($this->config->getSettings(Schema::MODULE_NAME)['site_title'])
                    ->setDescription($this->translator->t($feed, $feed))
                    ->assign($feedItems);

                $this->setContentType('text/xml');
                return $this->response->setContent($this->feedGenerator->generateFeed());
            } catch (\InvalidArgumentException $e) {
            }
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
