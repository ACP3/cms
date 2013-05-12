<?php
/**
 * Gallery
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['submit']) === true) {
	if (ACP3\Core\Validate::date($_POST['start'], $_POST['end']) === false)
		$errors[] = ACP3\CMS::$injector['Lang']->t('system', 'select_date');
	if (strlen($_POST['title']) < 3)
		$errors['title'] = ACP3\CMS::$injector['Lang']->t('gallery', 'type_in_gallery_title');
	if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']) &&
		(ACP3\Core\Validate::isUriSafe($_POST['alias']) === false || ACP3\Core\Validate::uriAliasExists($_POST['alias']) === true))
		$errors['alias'] = ACP3\CMS::$injector['Lang']->t('system', 'uri_alias_unallowed_characters_or_exists');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$insert_values = array(
			'id' => '',
			'start' => ACP3\CMS::$injector['Date']->toSQL($_POST['start']),
			'end' => ACP3\CMS::$injector['Date']->toSQL($_POST['end']),
			'title' => ACP3\Core\Functions::str_encode($_POST['title']),
			'user_id' => ACP3\CMS::$injector['Auth']->getUserId(),
		);

		$bool = ACP3\CMS::$injector['Db']->insert(DB_PRE . 'gallery', $insert_values);
		if ((bool) CONFIG_SEO_ALIASES === true && !empty($_POST['alias']))
			ACP3\Core\SEO::insertUriAlias('gallery/pics/id_' . ACP3\CMS::$injector['Db']->lastInsertId(), $_POST['alias'], $_POST['seo_keywords'], $_POST['seo_description'], (int) $_POST['seo_robots']);

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/gallery');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Datumsauswahl
	ACP3\CMS::$injector['View']->assign('publication_period', ACP3\CMS::$injector['Date']->datepicker(array('start', 'end')));

	ACP3\CMS::$injector['View']->assign('SEO_FORM_FIELDS', ACP3\Core\SEO::formFields());

	ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('title' => '', 'alias' => '', 'seo_keywords' => '', 'seo_description' => ''));

	ACP3\CMS::$injector['Session']->generateFormToken();
}
