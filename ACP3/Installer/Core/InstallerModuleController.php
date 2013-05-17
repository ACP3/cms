<?php
namespace ACP3\Installer\Core;

use ACP3\Core;

/**
 * Description of InstallerModuleController
 *
 * @author goratsch
 */
class InstallerModuleController extends Core\ModuleController {

	public function display()
	{
		$view = Core\Registry::get('View');
		// Content-Template automatisch setzen
		if ($view->getContentTemplate() === '') {
			$view->setContentTemplate(Core\Registry::get('URI')->mod . '/' . Core\Registry::get('URI')->file . '.tpl');
		}

		if ($view->getNoOutput() === false) {
			if ($view->getContent() === '') {
				$view->setContent($view->fetchTemplate($view->getContentTemplate()));
			}

			// Evtl. gesetzten Content-Type des Servers überschreiben
			header($view->getContentType());

			if ($view->getLayout() !== '') {
				$view->assign('PAGE_TITLE', 'ACP3 Installation');
				$view->assign('TITLE', Core\Registry::get('Lang')->t(Core\Registry::get('URI')->file));
				$view->assign('CONTENT', $view->getContent() . $view->getContentAppend());

				$view->displayTemplate($view->getLayout());
			} else {
				echo $view->getContent();
			}
		}
	}
}