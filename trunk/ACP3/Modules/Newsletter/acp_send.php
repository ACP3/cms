<?php
/**
 * Newsletter
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletters WHERE id = ?', array(ACP3\CMS::$injector['URI']->id)) == 1) {
	$settings = ACP3\Core\Config::getSettings('newsletter');
	$newsletter = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT title, text FROM ' . DB_PRE . 'newsletters WHERE id = ?', array(ACP3\CMS::$injector['URI']->id));

	$subject = html_entity_decode($newsletter['title'], ENT_QUOTES, 'UTF-8');
	$body = html_entity_decode($newsletter['text'] . "\n-- \n" . $settings['mailsig'], ENT_QUOTES, 'UTF-8');

	require_once MODULES_DIR . 'newsletter/functions.php';
	$bool = sendNewsletter($subject, $body, $settings['mail']);
	$bool2 = false;
	if ($bool === true) {
		$bool2 = ACP3\CMS::$injector['Db']->update(DB_PRE . 'newsletters', array('status' => '1'), array('id' => ACP3\CMS::$injector['URI']->id));
	}

	ACP3\Core\Functions::setRedirectMessage($bool && $bool2, ACP3\CMS::$injector['Lang']->t('newsletter', $bool === true && $bool2 !== false ? 'create_success' : 'create_save_error'), 'acp/newsletter');
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}