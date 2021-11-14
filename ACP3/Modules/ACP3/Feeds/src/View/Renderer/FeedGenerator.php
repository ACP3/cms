<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Feeds\View\Renderer;

use ACP3\Core\Router\RouterInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Feeds\Installer\Schema;
use FeedWriter\ATOM;

/**
 * Renderer for the output of RSS and ATOM News feeds.
 */
class FeedGenerator
{
    /**
     * @var SettingsInterface
     */
    private $config;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;
    /**
     * @var \FeedWriter\Feed|null
     */
    private $renderer;

    /**
     * @var array
     */
    private $settings = [];
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $description;

    public function __construct(
        SettingsInterface $config,
        RouterInterface $router
    ) {
        $this->config = $config;
        $this->router = $router;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    protected function configure(): void
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
        $this->renderer = new $className();
    }

    /**
     * Generates the channel element for a feed.
     */
    protected function generateChannel(): void
    {
        $link = $this->router->route('', true);
        $this->renderer->setTitle($this->title);
        $this->renderer->setLink($link);

        if ($this->renderer instanceof ATOM) {
            $this->renderer->setChannelElement('updated', date(DATE_ATOM));
            /* @phpstan-ignore-next-line */
            $this->renderer->setChannelElement('author', ['name' => $this->title]);
        } else {
            $this->renderer->setDescription($this->description);
        }

        if (!empty($this->settings['feed_image'])) {
            $this->renderer->setImage($this->settings['feed_image'], $this->title, $link);
        }
    }

    public function assign(array $items): void
    {
        $this->configure();

        // Check for a multidimensional array
        if (isset($items[0]) === true) {
            foreach ($items as $row) {
                $this->assign($row);
            }
        } elseif ($items) { // Single item
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

    public function generateFeed(): string
    {
        $this->configure();

        $this->generateChannel();

        return $this->renderer->generateFeed();
    }
}
