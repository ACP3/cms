<?php

namespace ACP3\Modules\ACP3\Feeds\Helper;

use ACP3\Core\Router\RouterInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Feeds\Installer\Schema;
use FeedWriter\ATOM;

/**
 * Renderer for the output of RSS and ATOM News feeds
 * @package ACP3\Modules\ACP3\Feeds\Helpe
 */
class FeedGenerator
{
    /**
     * @var SettingsInterface
     */
    protected $config;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    protected $router;
    /**
     * @var \FeedWriter\Feed
     */
    protected $renderer;

    /**
     * @var array
     */
    protected $settings = [];
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $description;

    /**
     * FeedGenerator constructor.
     * @param SettingsInterface $config
     * @param RouterInterface $router
     */
    public function __construct(
        SettingsInterface $config,
        RouterInterface $router)
    {
        $this->config = $config;
        $this->router = $router;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    protected function configure()
    {
        if ($this->renderer) {
            return;
        }

        $this->settings = $this->config->getSettings(Schema::MODULE_NAME);

        switch ($this->settings['feed_type']) {
            case 'ATOM':
                $feedType = 'ATOM';
                break;
            case 'RSS 1.0':
                $feedType = 'RSS1';
                break;
            default:
                $feedType = 'RSS2';
        }
        $className = '\\FeedWriter\\' . $feedType;
        $this->renderer = new $className;
    }

    /**
     * Generates the channel element for a feed
     */
    protected function generateChannel()
    {
        $link = $this->router->route('', true);
        $this->renderer->setTitle($this->title);
        $this->renderer->setLink($link);

        if ($this->renderer instanceof ATOM) {
            $this->renderer->setChannelElement('updated', date(DATE_ATOM, time()));
            $this->renderer->setChannelElement('author', ['name' => $this->title]);
        } else {
            $this->renderer->setDescription($this->description);
        }

        if (!empty($this->settings['feed_image'])) {
            $this->renderer->setImage($this->title, $link, $this->settings['feed_image']);
        }
    }

    /**
     * @param array $items
     */
    public function assign(array $items)
    {
        $this->configure();

        // Check for a multidimensional array
        if (isset($items[0]) === true) {
            foreach ($items as $row) {
                $this->assign($row);
            }
        } else { // Single item
            /** @var \FeedWriter\Item $item */
            $item = $this->renderer->createNewItem();
            $item->setTitle($items['title']);
            $item->setDate($items['date']);
            $item->setDescription($items['description']);
            $item->setLink($items['link']);
            if (!($this->renderer instanceof ATOM)) {
                $item->addElement('guid', $items['link'], ['isPermaLink' => 'true']);
            }
            $this->renderer->addItem($item);
        }
    }

    /**
     * @return string
     */
    public function generateFeed()
    {
        $this->configure();

        $this->generateChannel();
        return $this->renderer->generateFeed();
    }
}
