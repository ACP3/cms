<?php

namespace ACP3\Modules\Contact;

use ACP3\Core;

/**
 * Description of ContactAdmin
 *
 * @author Tino
 */
class ContactAdmin extends Core\ModuleController {

	public function __construct($injector)
	{
		parent::__construct($injector);
	}

	public function actionList()
	{
		if (isset($_POST['submit']) === true) {
			if (!empty($_POST['mail']) && Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = $this->injector['Lang']->t('system', 'wrong_email_format');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'address' => Core\Functions::str_encode($_POST['address'], true),
					'mail' => $_POST['mail'],
					'telephone' => Core\Functions::str_encode($_POST['telephone']),
					'fax' => Core\Functions::str_encode($_POST['fax']),
					'disclaimer' => Core\Functions::str_encode($_POST['disclaimer'], true),
				);

				$bool = Core\Config::setSettings('contact', $data);

				$this->injector['Session']->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/contact');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			Core\Functions::getRedirectMessage();

			$settings = Core\Config::getSettings('contact');

			$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $settings);

			$this->injector['Session']->generateFormToken();
		}
	}

}