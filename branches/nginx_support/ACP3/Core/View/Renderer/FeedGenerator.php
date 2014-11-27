<?php

namespace ACP3\Core\View\Renderer;

/**
 * Renderer for the output of RSS and ATOM newsfeeds
 * @package ACP3\Core\View\Renderer
 */
class FeedGenerator extends \ACP3\Core\View\AbstractRenderer
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
     * @param      $name
     * @param null $value
     */
    public function assign($name, $value = null)
    {
        if (is_array($name) === true) {
            $item = $this->renderer->createNewItem();
            $item->setTitle($name['title']);
            $item->setDate($name['date']);
            $item->setDescription($name['description']);
            $item->setLink($name['link']);
            if ($this->config['feed_type'] !== 'ATOM') {
                $item->addElement('guid', $name['link'], ['isPermaLink' => 'true']);
            }
            $this->renderer->addItem($item);
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
     * @param $template
     *
     * @return bool
     */
    public function templateExists($template)
    {
        return true;
    }
}
