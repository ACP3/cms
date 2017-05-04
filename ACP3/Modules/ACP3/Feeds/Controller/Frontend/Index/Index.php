<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Feeds;
use ACP3\Modules\ACP3\System\Installer\Schema;

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
     * @var Core\ACL
     */
    private $acl;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\ACL $acl
     * @param \ACP3\Modules\ACP3\Feeds\View\Renderer\FeedGenerator $feedGenerator
     * @param Feeds\Utility\FeedAvailabilityRegistrar $availableFeedsRegistrar
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\ACL $acl,
        Feeds\View\Renderer\FeedGenerator $feedGenerator,
        Feeds\Utility\FeedAvailabilityRegistrar $availableFeedsRegistrar
    ) {
        parent::__construct($context);

        $this->feedGenerator = $feedGenerator;
        $this->availableFeedsRegistrar = $availableFeedsRegistrar;
        $this->acl = $acl;
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

                $this->getResponse()->headers->set('Content-type', 'text/xml');
                return $this->response->setContent($this->feedGenerator->generateFeed());
            } catch (\InvalidArgumentException $e) {
            }
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
