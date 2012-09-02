<?php
/**
 * Emoticons
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();

$emoticons = ACP3_CMS::$db->select('id, code, description, img', 'emoticons', 0, 'id DESC', POS, ACP3_CMS::$auth->entries);
$c_emoticons = count($emoticons);

if ($c_emoticons > 0) {
	ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db->countRows('*', 'emoticons')));
	for ($i = 0; $i < $c_emoticons; ++$i) {
		$emoticons[$i]['code'] = ACP3_CMS::$db->escape($emoticons[$i]['code'], 3);
		$emoticons[$i]['description'] = ACP3_CMS::$db->escape($emoticons[$i]['description'], 3);
	}
	ACP3_CMS::$view->assign('emoticons', $emoticons);
	ACP3_CMS::$view->assign('can_delete', ACP3_Modules::check('emoticons', 'acp_delete'));
}
ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('emoticons/acp_list.tpl'));
