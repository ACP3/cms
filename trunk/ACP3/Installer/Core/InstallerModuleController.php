<?php
namespace ACP3\Installer\Core;

/**
 * Description of InstallerModuleController
 *
 * @author goratsch
 */
class InstallerModuleController extends \ACP3\Core\ModuleController {

	public function display()
	{
		$view = $this->injector['View'];
		// Content-Template automatisch setzen
		if ($view->getContentTemplate() === '') {
			$view->setContentTemplate($this->injector['URI']->mod . '/' . $this->injector['URI']->file . '.tpl');
		}

		if ($view->getNoOutput() === false) {
			if ($view->getContent() === '') {
				$view->setContent($view->fetchTemplate($view->getContentTemplate()));
			}

			// Evtl. gesetzten Content-Type des Servers überschreiben
			header($view->getContentType());

			if ($view->getLayout() !== '') {
				$view->assign('PAGE_TITLE', 'ACP3 Installation');
				$view->assign('TITLE', $this->injector['Lang']->t($this->injector['URI']->file));
				$view->assign('CONTENT', $view->getContent() . $view->getContentAppend());

				$view->displayTemplate($view->getLayout());
			} else {
				echo $view->getContent();
			}
		}
	}
}