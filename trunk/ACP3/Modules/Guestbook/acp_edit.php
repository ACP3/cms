<?php
/**
 * Guestbook
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'guestbook WHERE id = ?', array(ACP3\CMS::$injector['URI']->id)) == 1) {
	$settings = ACP3\Core\Config::getSettings('guestbook');

	if (isset($_POST['submit']) === true) {
		if (empty($_POST['name']))
			$errors['name'] = ACP3\CMS::$injector['Lang']->t('system', 'name_to_short');
		if (strlen($_POST['message']) < 3)
			$errors['message'] = ACP3\CMS::$injector['Lang']->t('system', 'message_to_short');
		if ($settings['notify'] == 2 && (!isset($_POST['active']) || ($_POST['active'] != 0 && $_POST['active'] != 1)))
			$errors['notify'] = ACP3\CMS::$injector['Lang']->t('guestbook', 'select_activate');

		if (isset($errors) === true) {
			ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
		} elseif (ACP3\Core\Validate::formToken() === false) {
			ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
		} else {
			$update_values = array(
				'name' => ACP3\Core\Functions::str_encode($_POST['name']),
				'message' => ACP3\Core\Functions::str_encode($_POST['message']),
				'active' => $settings['notify'] == 2 ? $_POST['active'] : 1,
			);

			$bool = ACP3\CMS::$injector['Db']->update(DB_PRE . 'guestbook', $update_values, array('id' => ACP3\CMS::$injector['URI']->id));

			ACP3\CMS::$injector['Session']->unsetFormToken();

			ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/guestbook');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$guestbook = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT name, message, active FROM ' . DB_PRE . 'guestbook WHERE id = ?', array(ACP3\CMS::$injector['URI']->id));

		if (ACP3\Core\Modules::check('emoticons', 'functions') === true && $settings['emoticons'] == 1) {
			require_once MODULES_DIR . 'emoticons/functions.php';

			//Emoticons im Formular anzeigen
			ACP3\CMS::$injector['View']->assign('emoticons', emoticonsList());
		}

		if ($settings['notify'] == 2) {
			$activate = array();
			$activate[0]['value'] = '1';
			$activate[0]['checked'] = ACP3\Core\Functions::selectEntry('active', '1', $guestbook['active'], 'checked');
			$activate[0]['lang'] = ACP3\CMS::$injector['Lang']->t('system', 'yes');
			$activate[1]['value'] = '0';
			$activate[1]['checked'] = ACP3\Core\Functions::selectEntry('active', '0', $guestbook['active'], 'checked');
			$activate[1]['lang'] = ACP3\CMS::$injector['Lang']->t('system', 'no');
			ACP3\CMS::$injector['View']->assign('activate', $activate);
		}

		ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $guestbook);

		ACP3\CMS::$injector['Session']->generateFormToken();
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
