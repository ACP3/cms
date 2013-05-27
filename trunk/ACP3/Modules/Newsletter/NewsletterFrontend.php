<?php

namespace ACP3\Modules\Newsletter;

use ACP3\Core;

/**
 * Description of NewsletterFrontend
 *
 * @author Tino Goratsch
 */
class NewsletterFrontend extends Core\ModuleController {

	public function actionActivate() {
		if (Core\Validate::email(Core\Registry::get('URI')->mail) && Core\Validate::isMD5(Core\Registry::get('URI')->hash)) {
			$mail = Core\Registry::get('URI')->mail;
			$hash = Core\Registry::get('URI')->hash;
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}

		if (Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ? AND has = ?', array($mail, $hash)) != 1)
			$errors[] = Core\Registry::get('Lang')->t('newsletter', 'account_not_exists');

		if (isset($errors) === true) {
			Core\Registry::get('View')->setContent(Core\Functions::errorBox($errors));
		} else {
			$bool = Core\Registry::get('Db')->update(DB_PRE . 'newsletter_accounts', array('hash' => ''), array('mail' => $mail, 'hash' => $hash));

			Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), ROOT_DIR));
		}
	}

	public function actionArchive() {
		Core\Registry::get('Breadcrumb')->append(Core\Registry::get('Lang')->t('newsletter', 'archive'));

		if (isset($_POST['newsletter']) === true &&
				Core\Validate::isNumber($_POST['newsletter'])) {
			$id = (int) $_POST['newsletter'];

			$newsletter = Core\Registry::get('Db')->fetchAssoc('SELECT date, title, text FROM ' . DB_PRE . 'newsletters WHERE id = ? AND status = ?', array($id, 1));
			if (!empty($newsletter)) {
				$newsletter['date_formatted'] = Core\Registry::get('Date')->format($newsletter['date'], 'short');
				$newsletter['date_iso'] = Core\Registry::get('Date')->format($newsletter['date'], 'c');
				$newsletter['text'] = Core\Functions::nl2p($newsletter['text']);

				Core\Registry::get('View')->assign('newsletter', $newsletter);
			}
		}

		$newsletters = Core\Registry::get('Db')->fetchAll('SELECT id, date, title FROM ' . DB_PRE . 'newsletters WHERE status = ? ORDER BY date DESC', array(1));
		$c_newsletters = count($newsletters);

		if ($c_newsletters > 0) {
			for ($i = 0; $i < $c_newsletters; ++$i) {
				$newsletters[$i]['date_formatted'] = Core\Registry::get('Date')->format($newsletters[$i]['date'], 'short');
				$newsletters[$i]['selected'] = Core\Functions::selectEntry('newsletter', $newsletters[$i]['id']);
			}
			Core\Registry::get('View')->assign('newsletters', $newsletters);
		}
	}

	public function actionList() {
		$captchaAccess = Core\Modules::hasPermission('captcha', 'image');

		if (isset($_POST['submit']) === true) {
			switch (Core\Registry::get('URI')->action) {
				case 'subscribe':
					if (Core\Validate::email($_POST['mail']) === false)
						$errors['mail'] = Core\Registry::get('Lang')->t('system', 'wrong_email_format');
					if (Core\Validate::email($_POST['mail']) && Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ?', array($_POST['mail'])) == 1)
						$errors['mail'] = Core\Registry::get('Lang')->t('newsletter', 'account_exists');
					if ($captchaAccess === true && Core\Registry::get('Auth')->isUser() === false && Core\Validate::captcha($_POST['captcha']) === false)
						$errors['captcha'] = Core\Registry::get('Lang')->t('captcha', 'invalid_captcha_entered');

					if (isset($errors) === true) {
						Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
					} elseif (Core\Validate::formToken() === false) {
						Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
					} else {
						$bool = NewsletterFunctions::subscribeToNewsletter($_POST['mail']);

						Core\Registry::get('Session')->unsetFormToken();

						Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('newsletter', $bool !== false ? 'subscribe_success' : 'subscribe_error'), ROOT_DIR));
					}
					break;
				case 'unsubscribe':
					if (Core\Validate::email($_POST['mail']) === false)
						$errors[] = Core\Registry::get('Lang')->t('system', 'wrong_email_format');
					if (Core\Validate::email($_POST['mail']) && Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ?', array($_POST['mail'])) != 1)
						$errors[] = Core\Registry::get('Lang')->t('newsletter', 'account_not_exists');
					if ($captchaAccess === true && Core\Registry::get('Auth')->isUser() === false && Core\Validate::captcha($_POST['captcha']) === false)
						$errors[] = Core\Registry::get('Lang')->t('captcha', 'invalid_captcha_entered');

					if (isset($errors) === true) {
						Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
					} elseif (Core\Validate::formToken() === false) {
						Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
					} else {
						$bool = Core\Registry::get('Db')->delete(DB_PRE . 'newsletter_accounts', array('mail' => $_POST['mail']));

						Core\Registry::get('Session')->unsetFormToken();

						Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('newsletter', $bool !== false ? 'unsubscribe_success' : 'unsubscribe_error'), ROOT_DIR));
					}
					break;
				default:
					Core\Registry::get('URI')->redirect('errors/404');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : array('mail' => ''));

			$field_value = Core\Registry::get('URI')->action ? Core\Registry::get('URI')->action : 'subscribe';

			$actions_Lang = array(
				Core\Registry::get('Lang')->t('newsletter', 'subscribe'),
				Core\Registry::get('Lang')->t('newsletter', 'unsubscribe')
			);
			Core\Registry::get('View')->assign('actions', Core\Functions::selectGenerator('action', array('subscribe', 'unsubstribe'), $actions_Lang, $field_value, 'checked'));

			if ($captchaAccess === true) {
				Core\Registry::get('View')->assign('captcha', \ACP3\Modules\Captcha\CaptchaFunctions::captcha());
			}

			Core\Registry::get('Session')->generateFormToken();
		}
	}

	public function actionSidebar() {
		if (Core\Modules::hasPermission('captcha', 'image') === true) {
			Core\Registry::get('View')->assign('captcha', \ACP3\Modules\Captcha\CaptchaFunctions::captcha());
		}

		Core\Registry::get('Session')->generateFormToken('newsletter/list');

		Core\Registry::get('View')->displayTemplate('newsletter/sidebar.tpl');
	}

}