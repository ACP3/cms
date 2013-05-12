<?php

namespace ACP3\Core;

/**
 * Description of ModuleController
 *
 * @author Tino
 */
abstract class ModuleController {

	protected $injector;

	public function __construct(Pimple $injector)
	{
		$this->injector = $injector;
	}

	public function display()
	{
		// Content-Template automatisch setzen
		$this->injector['View']->setContentTemplate($this->injector['URI']->mod . '/' . $this->injector['URI']->file . '.tpl');

		if ($this->injector['View']->getNoOutput() === false) {
			if ($this->injector['View']->getContent() === '') {
				$this->injector['View']->setContent($this->injector['View']->fetchTemplate($this->injector['View']->getContentTemplate()));
			}

			// Evtl. gesetzten Content-Type des Servers überschreiben
			header($this->injector['View']->getContentType());

			if ($this->injector['View']->getLayout() !== '') {
				$this->injector['View']->assign('PAGE_TITLE', CONFIG_SEO_TITLE);
				$this->injector['View']->assign('HEAD_TITLE', $this->injector['Breadcrumb']->output(3));
				$this->injector['View']->assign('TITLE', $this->injector['Breadcrumb']->output(2));
				$this->injector['View']->assign('BREADCRUMB', $this->injector['Breadcrumb']->output());
				$this->injector['View']->assign('META', SEO::getMetaTags());
				$this->injector['View']->assign('CONTENT', $this->injector['View']->getContent() . $this->injector['View']->getContentAppend());

				$minify = $this->injector['View']->buildMinifyLink();
				$file = $this->injector['View']->getLayout();
				$layout = substr($file, 0, strpos($file, '.'));
				$this->injector['View']->assign('MIN_STYLESHEET', sprintf($minify, 'css') . ($layout !== 'layout' ? '&amp;layout=' . $layout : ''));
				$this->injector['View']->assign('MIN_JAVASCRIPT', sprintf($minify, 'js'));

				$this->injector['View']->displayTemplate($file);
			} else {
				echo $this->injector['View']->getContent();
			}
		}
	}

}