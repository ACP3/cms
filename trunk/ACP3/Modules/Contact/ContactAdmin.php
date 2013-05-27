<?php

namespace ACP3\Modules\Contact;

use ACP3\Core;

/**
 * Description of ContactAdmin
 *
 * @author Tino Goratsch
 */
class ContactAdmin extends Core\ModuleController {

	public function actionList()
	{
		if (isset($_POST['submit']) === true) {
			if (!empty($_POST['mail']) && Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = Core\Registry::get('Lang')->t('system', 'wrong_email_format');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'address' => Core\Functions::strEncode($_POST['address'], true),
					'mail' => $_POST['mail'],
					'telephone' => Core\Functions::strEncode($_POST['telephone']),
					'fax' => Core\Functions::strEncode($_POST['fax']),
					'disclaimer' => Core\Functions::strEncode($_POST['disclaimer'], true),
				);

				$bool = Core\Config::setSettings('contact', $data);

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/contact');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			Core\Functions::getRedirectMessage();

			$settings = Core\Config::getSettings('contact');

			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $settings);

			Core\Registry::get('Session')->generateFormToken();
		}
	}

}