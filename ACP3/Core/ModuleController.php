<?php

namespace ACP3\Core;

/**
 * Description of ModuleController
 *
 * @author Tino
 */
abstract class ModuleController {

	public function display()
	{
		$view = Registry::get('View');
		// Content-Template automatisch setzen
		if ($view->getContentTemplate() === '') {
			$view->setContentTemplate(Registry::get('URI')->mod . '/' . Registry::get('URI')->file . '.tpl');
		}

		if ($view->getNoOutput() === false) {
			if ($view->getContent() === '') {
				$view->setContent($view->fetchTemplate($view->getContentTemplate()));
			}

			// Evtl. gesetzten Content-Type des Servers überschreiben
			header($view->getContentType());

			if ($view->getLayout() !== '') {
				$view->assign('PAGE_TITLE', CONFIG_SEO_TITLE);
				$view->assign('HEAD_TITLE', Registry::get('Breadcrumb')->output(3));
				$view->assign('TITLE', Registry::get('Breadcrumb')->output(2));
				$view->assign('BREADCRUMB', Registry::get('Breadcrumb')->output());
				$view->assign('META', SEO::getMetaTags());
				$view->assign('CONTENT', $view->getContent() . $view->getContentAppend());

				$minify = $view->buildMinifyLink();
				$file = $view->getLayout();
				$layout = substr($file, 0, strpos($file, '.'));
				$view->assign('MIN_STYLESHEET', sprintf($minify, 'css') . ($layout !== 'layout' ? '&amp;layout=' . $layout : ''));
				$view->assign('MIN_JAVASCRIPT', sprintf($minify, 'js'));

				$view->displayTemplate($file);
			} else {
				echo $view->getContent();
			}
		}
	}

}