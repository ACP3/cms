<?php
/**
 * Users
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
	$entries = $_POST['entries'];
elseif (ACP3_Validate::deleteEntries(ACP3_CMS::$uri->entries) === true)
	$entries = ACP3_CMS::$uri->entries;

if (!isset($entries)) {
	ACP3_CMS::$view->setContent(errorBox(ACP3_CMS::$lang->t('system', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_CMS::$view->setContent(confirmBox(ACP3_CMS::$lang->t('system', 'confirm_delete'), ACP3_CMS::$uri->route('acp/users/delete/entries_' . $marked_entries . '/action_confirmed/'), ACP3_CMS::$uri->route('acp/users')));
} elseif (ACP3_CMS::$uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = false;
	$admin_user = false;
	$self_delete = false;
	foreach ($marked_entries as $entry) {
		if ($entry == 1) {
			$admin_user = true;
		} else {
			// Falls sich der User selbst gelöscht hat, diesen auch gleich abmelden
			if ($entry == ACP3_CMS::$auth->getUserId()) {
				ACP3_CMS::$auth->logout();
				$self_delete = true;
			}
			$bool = ACP3_CMS::$db2->delete(DB_PRE . 'users', array('id' => $entry));
		}
	}
	if ($admin_user === true) {
		$bool = false;
		$text = ACP3_CMS::$lang->t('users', 'admin_user_undeletable');
	} else {
		$text = ACP3_CMS::$lang->t('system', $bool !== false ? 'delete_success' : 'delete_error');
	}
	setRedirectMessage($bool, $text, $self_delete === true ? ROOT_DIR : 'acp/users');
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
