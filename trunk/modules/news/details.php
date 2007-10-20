<?php
/**
 * News
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

$date = ' AND (start = end AND start <= \'' . date_aligned(2, time()) . '\' OR start != end AND start <= \'' . date_aligned(2, time()) . '\' AND end >= \'' . date_aligned(2, time()) . '\')';

if (!empty($modules->id) && $db->select('id', 'news', 'id = \'' . $modules->id . '\'' . $date, 0, 0, 0, 1) == 1) {
	if (isset($_POST['submit'])) {
		include_once 'modules/comments/entry.php';
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
		if ($auth->is_user() && preg_match('/\d/', $_SESSION['acp3_id'])) {
			$user = $auth->getUserInfo('nickname');
			$disabled = ' readonly="readonly" class="readonly"';

			if (isset($form)) {
				$form['name'] = $user['nickname'];
				$form['name_disabled'] = $disabled;
			} else {
				$user['name'] = $user['nickname'];
				$user['name_disabled'] = $disabled;
			}
			unset($user['nickname']);
			$tpl->assign('form', isset($form) ? $form : $user);
		} else {
			$tpl->assign('form', isset($form) ? $form : array('name_disabled' => ''));
		}

		// News Cache erstellen
		if (!$cache->check('news_details_id_' . $modules->id)) {
			$cache->create('news_details_id_' . $modules->id, $db->select('id, start, headline, text, category_id, uri, target, link_title', 'news', 'id = \'' . $modules->id . '\''));
		}
		$news = $cache->output('news_details_id_' . $modules->id);

		// Brotkrümelspur
		$breadcrumb->assign(lang('news', 'news'), uri('news'));
		$category = $db->select('name', 'categories', 'id = \'' . $news[0]['category_id'] . '\'');
		$breadcrumb->assign($category[0]['name'], uri('news/list/cat_' . $news[0]['category_id']));
		$breadcrumb->assign($news[0]['headline']);

		$news[0]['date'] = date_aligned(1, $news[0]['start']);
		$news[0]['headline'] = $news[0]['headline'];
		$news[0]['text'] = $db->escape($news[0]['text'], 3);
		$news[0]['uri'] = $db->escape($news[0]['uri'], 3);
		$news[0]['target'] = $news[0]['target'] == '2' ? ' onclick="window.open(this.href); return false"' : '';

		$tpl->assign('news', $news[0]);

		if ($modules->check('comments', 'functions', 'frontend')) {
			include_once 'modules/comments/functions.php';

			$tpl->assign('comments_list', comments_list());
			$tpl->assign('comments_form', comments_form());
		}
		$content = $tpl->fetch('news/details.html');
	}
} else {
	redirect('errors/404');
}
?>