<?php

namespace ACP3\Installer\Core\Modules;

use ACP3\Core;

/**
 * Module Controller of the installer modules
 *
 * @author Tino Goratsch
 */
class Controller {

	/**
	 *
	 * @var \ACP3\Core\URI
	 */
	protected $uri;

	/**
	 *
	 * @var \ACP3\Core\View
	 */
	protected $view;

	public function __construct() {
		$this->lang = \ACP3\Core\Registry::get('Lang');
		$this->uri = \ACP3\Core\Registry::get('URI');
		$this->view = \ACP3\Core\Registry::get('View');
	}

	public function display() {
		// Content-Template automatisch setzen
		if ($this->view->getContentTemplate() === '') {
			$this->view->setContentTemplate($this->uri->mod . '/' . $this->uri->file . '.tpl');
		}

		if ($this->view->getNoOutput() === false) {
			if ($this->view->getContent() === '') {
				$this->view->setContent($this->view->fetchTemplate($this->view->getContentTemplate()));
			}

			// Evtl. gesetzten Content-Type des Servers überschreiben
			header($this->view->getContentType());

			if ($this->view->getLayout() !== '') {
				$this->view->assign('PAGE_TITLE', 'ACP3 Installation');
				$this->view->assign('TITLE', $this->lang->t($this->uri->file));
				$this->view->assign('CONTENT', $this->view->getContent() . $this->view->getContentAppend());

				$this->view->displayTemplate($this->view->getLayout());
			} else {
				echo $this->view->getContent();
			}
		}
	}

}
