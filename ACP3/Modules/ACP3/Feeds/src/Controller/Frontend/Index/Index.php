<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Feeds;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\HttpFoundation\Response;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Modules\ACP3\Feeds\View\Renderer\FeedGenerator
     */
    private $feedGenerator;
    /**
     * @var Feeds\Utility\FeedAvailabilityRegistrar
     */
    private $availableFeedsRegistrar;
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;

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
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute(string $feed): Response
    {
        if ($this->acl->hasPermission('frontend/' . $feed) === true) {
            try {
                $feedItems = $this->availableFeedsRegistrar
                    ->getFeedItemsByModuleName($feed)
                    ->fetchFeedItems();

                $this->feedGenerator
                    ->setTitle($this->config->getSettings(Schema::MODULE_NAME)['site_title'])
                    ->setDescription($this->translator->t($feed, $feed))
                    ->assign($feedItems);

                $response = new Response($this->feedGenerator->generateFeed(), Response::HTTP_OK, [
                    'Content-type' => 'text/xml',
                ]);

                $this->setCacheResponseCacheable(
                    $this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime'],
                    $response
                );

                return $response;
            } catch (\InvalidArgumentException $e) {
            }
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }
}
