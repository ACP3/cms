<?php
namespace ACP3\Core\View;

class FeedGenerator extends AbstractRenderer {
	public function __construct($params)
	{
		parent::__construct($params);

		require_once LIBRARIES_DIR . 'feedcreator/FeedWriter.php';
		require_once LIBRARIES_DIR . 'feedcreator/FeedItem.php';

		$this->renderer = new \FeedWriter($this->config['feed_type']);
		$this->generateChannel();
	}

	public function assign($name, $value = null)
	{
		$item = $this->renderer->createNewItem();
		if (is_array($name) === true) {
			$item->setTitle($name['title']);
			$item->setDate($name['date']);
			$item->setDescription($name['description']);
			$item->setLink($name['link']);
			if ($this->config['feed_type'] !== 'ATOM') {
				$item->addElement('guid', $name['link'], array('isPermaLink' => 'true'));
			}
		} elseif (is_null($value) === false) {
			$item->addElement($name, $value);
		}
		$this->renderer->addItem($item);
	}

	private function generateChannel()
	{
		$link = $this->config['feed_link'];
		$this->renderer->setTitle($this->config['feed_title']);
		$this->renderer->setLink($link);
		if ($this->config['feed_type'] !== 'ATOM') {
			$this->renderer->setDescription(\ACP3\Core\Registry::get('Lang')->t($this->config['module'], $this->config['module']));
		} else {
			$this->renderer->setChannelElement('updated', date(DATE_ATOM , time()));
			$this->renderer->setChannelElement('author', array('name' => $this->config['feed_title']));
		}

		if (!empty($this->config['feed_image']))
			$this->renderer->setImage($this->config['feed_title'], $link, $this->config['feed_image']);
	}

	public function fetch($type)
	{
		$this->renderer->setType($type);
		return $this->renderer->generateFeed();
	}

	public function display($type)
	{
		echo $this->fetch($type);
	}

	public function templateExists($template) {
		return true;
	}
}