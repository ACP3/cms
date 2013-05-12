<?php
/**
 * Articles
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE id = ?', array(ACP3\CMS::$injector['URI']->id)) == 1) {
	require_once MODULES_DIR . 'articles/functions.php';

	if (isset($_POST['submit']) === true) {
		if (ACP3\Core\Validate::date($_POST['start'], $_POST['end']) === false)
			$errors[] = ACP3\CMS::$injector['Lang']->t('system', 'select_date');
		if (strlen($_POST['title']) < 3)
			$errors['title'] = ACP3\CMS::$injector['Lang']->t('articles', 'title_to_short');
		if (strlen($_POST['text']) < 3)
			$errors['text'] = ACP3\CMS::$injector['Lang']->t('articles', 'text_to_short');
		if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
			(ACP3\Core\Validate::isUriSafe($_POST['alias']) === false || ACP3\Core\Validate::uriAliasExists($_POST['alias'], 'articles/details/id_' . ACP3\CMS::$injector['URI']->id) === true))
			$errors['alias'] = ACP3\CMS::$injector['Lang']->t('system', 'uri_alias_unallowed_characters_or_exists');

		if (isset($errors) === true) {
			ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
		} elseif (ACP3\Core\Validate::formToken() === false) {
			ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
		} else {
			$update_values = array(
				'start' => ACP3\CMS::$injector['Date']->toSQL($_POST['start']),
				'end' => ACP3\CMS::$injector['Date']->toSQL($_POST['end']),
				'title' => ACP3\Core\Functions::str_encode($_POST['title']),
				'text' => ACP3\Core\Functions::str_encode($_POST['text'], true),
				'user_id' => ACP3\CMS::$injector['Auth']->getUserId(),
			);

			$bool = ACP3\CMS::$injector['Db']->update(DB_PRE . 'articles', $update_values, array('id' => ACP3\CMS::$injector['URI']->id));
			if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
				ACP3\Core\SEO::insertUriAlias('articles/details/id_' . ACP3\CMS::$injector['URI']->id, $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

			setArticlesCache(ACP3\CMS::$injector['URI']->id);

			// Aliase in der Navigation aktualisieren
			require_once MODULES_DIR . 'menus/functions.php';
			setMenuItemsCache();

			ACP3\CMS::$injector['Session']->unsetFormToken();

			ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/articles');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$page = getArticlesCache(ACP3\CMS::$injector['URI']->id);

		// Datumsauswahl
		ACP3\CMS::$injector['View']->assign('publication_period', ACP3\CMS::$injector['Date']->datepicker(array('start', 'end'), array($page['start'], $page['end'])));

		ACP3\CMS::$injector['View']->assign('SEO_FORM_FIELDS', ACP3\Core\SEO::formFields('articles/details/id_' . ACP3\CMS::$injector['URI']->id));

		ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $page);

		ACP3\CMS::$injector['Session']->generateFormToken();
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
