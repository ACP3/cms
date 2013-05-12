<?php
/**
 * Files
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
	$entries = $_POST['entries'];
elseif (ACP3\Core\Validate::deleteEntries(ACP3\CMS::$injector['URI']->entries) === true)
	$entries = ACP3\CMS::$injector['URI']->entries;

if (!isset($entries)) {
	ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3\CMS::$injector['View']->setContent(confirmBox(ACP3\CMS::$injector['Lang']->t('system', 'confirm_delete'), ACP3\CMS::$injector['URI']->route('acp/files/delete/entries_' . $marked_entries . '/action_confirmed/'), ACP3\CMS::$injector['URI']->route('acp/files')));
} elseif (ACP3\CMS::$injector['URI']->action === 'confirmed') {
	require_once MODULES_DIR . 'files/functions.php';

	$marked_entries = explode('|', $entries);
	$bool = false;
	$commentsInstalled = ACP3\Core\Modules::isInstalled('comments');
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'files WHERE id = ?', array($entry)) == 1) {
			// Datei ebenfalls löschen
			$file = ACP3\CMS::$injector['Db']->fetchColumn('SELECT file FROM ' . DB_PRE . 'files WHERE id = ?', array($entry));
			removeUploadedFile('files', $file);
			$bool = ACP3\CMS::$injector['Db']->delete(DB_PRE . 'files', array('id' => $entry));
			if ($commentsInstalled === true)
				ACP3\CMS::$injector['Db']->delete(DB_PRE . 'comments', array('module' => 'files', 'entry_id' => $entry));

			ACP3\Core\Cache::delete('details_id_' . $entry, 'files');
			ACP3\Core\SEO::deleteUriAlias('files/details/id_' . $entry);
		}
	}
	ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/files');
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
