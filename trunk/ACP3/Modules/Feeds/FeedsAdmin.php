<?php

namespace ACP3\Modules\Feeds;

use ACP3\Core;

/**
 * Description of FeedsAdmin
 *
 * @author Tino
 */
class FeedsAdmin extends Core\ModuleController {

	public function __construct($injector) {
		parent::__construct($injector);
	}

	public function actionList() {
		if (isset($_POST['submit']) === true) {
			if (empty($_POST['feed_type']) || in_array($_POST['feed_type'], array('RSS 1.0', 'RSS 2.0', 'ATOM')) === false)
				$errors['mail'] = $this->injector['Lang']->t('feeds', 'select_feed_type');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'feed_image' => Core\Functions::str_encode($_POST['feed_image']),
					'feed_type' => $_POST['feed_type']
				);

				$bool = Core\Config::setSettings('feeds', $data);

				$this->injector['Session']->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/feeds');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			Core\Functions::getRedirectMessage();

			$settings = Core\Config::getSettings('feeds');

			$feed_type = array(
				'RSS 1.0',
				'RSS 2.0',
				'ATOM'
			);
			$this->injector['View']->assign('feed_types', Core\Functions::selectGenerator('feed_type', $feed_type, $feed_type, $settings['feed_type']));

			$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $settings);

			$this->injector['Session']->generateFormToken();
		}
	}

}