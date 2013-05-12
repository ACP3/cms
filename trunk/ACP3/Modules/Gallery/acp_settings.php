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

$settings = ACP3\Core\Config::getSettings('gallery');
$comments_active = ACP3\Core\Modules::isActive('comments');

if (isset($_POST['submit']) === true) {
	if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
		$errors['dateformat'] = ACP3\CMS::$injector['Lang']->t('system', 'select_date_format');
	if (ACP3\Core\Validate::isNumber($_POST['sidebar']) === false)
		$errors['sidebar'] = ACP3\CMS::$injector['Lang']->t('system', 'select_sidebar_entries');
	if (!isset($_POST['overlay']) || $_POST['overlay'] != 1 && $_POST['overlay'] != 0)
		$errors[] = ACP3\CMS::$injector['Lang']->t('gallery', 'select_use_overlay');
	if ($comments_active === true && (!isset($_POST['comments']) || $_POST['comments'] != 1 && $_POST['comments'] != 0))
		$errors[] = ACP3\CMS::$injector['Lang']->t('gallery', 'select_allow_comments');
	if (ACP3\Core\Validate::isNumber($_POST['thumbwidth']) === false || ACP3\Core\Validate::isNumber($_POST['width']) === false || ACP3\Core\Validate::isNumber($_POST['maxwidth']) === false)
		$errors[] = ACP3\CMS::$injector['Lang']->t('gallery', 'invalid_image_width_entered');
	if (ACP3\Core\Validate::isNumber($_POST['thumbheight']) === false || ACP3\Core\Validate::isNumber($_POST['height']) === false || ACP3\Core\Validate::isNumber($_POST['maxheight']) === false)
		$errors[] = ACP3\CMS::$injector['Lang']->t('gallery', 'invalid_image_height_entered');
	if (ACP3\Core\Validate::isNumber($_POST['filesize']) === false)
		$errors['filesize'] = ACP3\CMS::$injector['Lang']->t('gallery', 'invalid_image_filesize_entered');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$data = array(
			'width' => (int) $_POST['width'],
			'height' => (int) $_POST['height'],
			'thumbwidth' => (int) $_POST['thumbwidth'],
			'thumbheight' => (int) $_POST['thumbheight'],
			'maxwidth' => (int) $_POST['maxwidth'],
			'maxheight' => (int) $_POST['maxheight'],
			'filesize' => (int) $_POST['filesize'],
			'overlay' => $_POST['overlay'],
			'dateformat' => ACP3\Core\Functions::str_encode($_POST['dateformat']),
			'sidebar' => (int) $_POST['sidebar'],
		);
		if ($comments_active === true)
			$data['comments'] = $_POST['comments'];

		$bool = ACP3\Core\Config::setSettings('gallery', $data);

		// Falls sich die anzuzeigenden Bildgrößen geändert haben, die gecacheten Bilder löschen
		if ($_POST['thumbwidth'] !== $settings['thumbwidth'] || $_POST['thumbheight'] !== $settings['thumbheight'] ||
			$_POST['width'] !== $settings['width'] || $_POST['height'] !== $settings['height']) {
			ACP3\Core\Cache::purge('images', 'gallery');
			ACP3\Core\Cache::purge('sql', 'gallery');
		}

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/gallery');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	if ($comments_active === true) {
		$lang_comments = array(ACP3\CMS::$injector['Lang']->t('system', 'yes'), ACP3\CMS::$injector['Lang']->t('system', 'no'));
		ACP3\CMS::$injector['View']->assign('comments', ACP3\Core\Functions::selectGenerator('comments', array(1, 0), $lang_comments, $settings['comments'], 'checked'));
	}

	$lang_overlay = array(ACP3\CMS::$injector['Lang']->t('system', 'yes'), ACP3\CMS::$injector['Lang']->t('system', 'no'));
	ACP3\CMS::$injector['View']->assign('overlay', ACP3\Core\Functions::selectGenerator('overlay', array(1, 0), $lang_overlay, $settings['overlay'], 'checked'));

	ACP3\CMS::$injector['View']->assign('dateformat', ACP3\CMS::$injector['Date']->dateformatDropdown($settings['dateformat']));

	ACP3\CMS::$injector['View']->assign('sidebar_entries', ACP3\Core\Functions::recordsPerPage((int) $settings['sidebar'], 1, 10));

	ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	ACP3\CMS::$injector['Session']->generateFormToken();
}
