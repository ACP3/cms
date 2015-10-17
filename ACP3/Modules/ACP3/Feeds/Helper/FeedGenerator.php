<?php

namespace ACP3\Modules\ACP3\Feeds\Helper;

use ACP3\Core\Lang;

/**
 * Renderer for the output of RSS and ATOM News feeds
 * @package ACP3\Core\View\Renderer
 */
class FeedGenerator
{
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \FeedWriter\Feed
     */
    public $renderer;
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @param \ACP3\Core\Lang $lang
     */
    public function __construct(Lang $lang)
    {
        $this->lang = $lang;
    }

    /**
     * @param array $params
     */
    public function configure(array $params)
    {
        switch ($params['feed_type']) {
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

        $this->config = $params;

        $this->generateChannel();
    }

    /**
     * Generates the channel element for a feed
     */
    protected function generateChannel()
    {
        $link = $this->config['feed_link'];
        $this->renderer->setTitle($this->config['feed_title']);
        $this->renderer->setLink($link);
        if ($this->config['feed_type'] !== 'ATOM') {
            $this->renderer->setDescription($this->lang->t($this->config['module'], $this->config['module']));
        } else {
            $this->renderer->setChannelElement('updated', date(DATE_ATOM, time()));
            $this->renderer->setChannelElement('author', ['name' => $this->config['feed_title']]);
        }

        if (!empty($this->config['feed_image'])) {
            $this->renderer->setImage($this->config['feed_title'], $link, $this->config['feed_image']);
        }
    }

    /**
     * @param array $items
     */
    public function assign(array $items)
    {
        if ($this->renderer === null) {
            return;
        }

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
            if ($this->config['feed_type'] !== 'ATOM') {
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
        return $this->renderer->generateFeed();
    }
}
