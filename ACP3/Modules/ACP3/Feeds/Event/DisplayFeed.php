<?php
namespace ACP3\Modules\ACP3\Feeds\Event;

use ACP3\Core\View;
use ACP3\Modules\ACP3\Feeds\Helper\FeedGenerator;
use FeedWriter\Feed;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class DisplayFeed
 * @package ACP3\Modules\ACP3\Feeds\Event
 */
class DisplayFeed extends Event
{
    /**
     * @var \ACP3\Modules\ACP3\Feeds\Helper\FeedGenerator
     */
    protected $feedGenerator;

    /**
     * @param \ACP3\Modules\ACP3\Feeds\Helper\FeedGenerator $feedGenerator
     */
    public function __construct(FeedGenerator $feedGenerator)
    {
        $this->feedGenerator = $feedGenerator;
    }

    /**
     * @return \ACP3\Core\View
     */
    public function getFeedGenerator()
    {
        return $this->feedGenerator;
    }
}