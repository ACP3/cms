<?php

namespace ACP3\Core\View\Renderer;

/**
 * Renderer for the output of RSS and ATOM News feeds
 * @package ACP3\Core\View\Renderer
 */
class FeedGenerator extends AbstractRenderer
{
    /**
     * @var \FeedWriter\Feed
     */
    public $renderer;

    /**
     * @param array $params
     */
    public function configure(array $params = [])
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
            $this->renderer->setDescription($this->container->get('core.lang')->t($this->config['module'], $this->config['module']));
        } else {
            $this->renderer->setChannelElement('updated', date(DATE_ATOM, time()));
            $this->renderer->setChannelElement('author', ['name' => $this->config['feed_title']]);
        }

        if (!empty($this->config['feed_image'])) {
            $this->renderer->setImage($this->config['feed_title'], $link, $this->config['feed_image']);
        }
    }

    /**
     * @param      $items
     * @param null $value
     */
    public function assign($items, $value = null)
    {
        if (is_array($items) === true && !empty($items)) {
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
    }

    /**
     * @param $type
     */
    public function display($type)
    {
        echo $this->fetch($type);
    }

    /**
     * @param $type
     *
     * @return mixed
     */
    public function fetch($type)
    {
        return $this->renderer->generateFeed();
    }

    /**
     * @inheritdoc
     */
    public function templateExists($template)
    {
        return true;
    }
}
