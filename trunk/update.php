<?php
header('Content-type: text/plain; charset=UTF-8');

define('NEW_VERSION', '4.0RC2 SVN');
define('ACP3_ROOT', './');

require ACP3_ROOT . 'includes/config.php';

require ACP3_ROOT . 'includes/classes/cache.php';
require ACP3_ROOT . 'includes/classes/config.php';
require ACP3_ROOT . 'includes/classes/db.php';

$queries = array(
	'ALTER TABLE `{pre}access` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}categories` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}comments` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}comments` CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}comments` CHANGE `entry_id` `entry_id` INT(10) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}comments` ADD INDEX `foreign_entry_id` (`entry_id`)',
	'UPDATE {pre}comments SET name = \'\' WHERE user_id != 0',
	'ALTER TABLE `{pre}emoticons` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}files` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}files` CHANGE `category_id` `category_id` INT(10) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}files` ADD `comments` TINYINT(1) UNSIGNED NOT NULL AFTER `text`',
	'ALTER TABLE `{pre}files` ADD INDEX `foreign_category_id` (`category_id`)',
	'ALTER TABLE `{pre}gallery` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}gallery_pictures` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}gallery_pictures` CHANGE `gallery_id` `gallery_id` INT(10) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}gallery_pictures` ADD `comments` TINYINT(1) UNSIGNED NOT NULL AFTER `description`',
	'ALTER TABLE `{pre}gallery_pictures` ADD INDEX `foreign_gallery_id` (`gallery_id`)',
	'ALTER TABLE `{pre}guestbook` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}guestbook` CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}news` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}news` ADD `readmore` TINYINT(1) UNSIGNED NOT NULL AFTER `text`',
	'ALTER TABLE `{pre}news` ADD `comments` TINYINT(1) UNSIGNED NOT NULL AFTER `readmore`',
	'UPDATE `{pre}news` SET readmore = 1, comments = 1',
	'ALTER TABLE `{pre}news` CHANGE `category_id` `category_id` INT(10) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}news` CHANGE `target` `target` TINYINT(1) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}news` ADD INDEX `foreign_category_id` (`category_id`)',
	'RENAME TABLE `{pre}nnewsletter_accounts` TO `{pre}newsletter_accounts`',
	'ALTER TABLE `{pre}newsletter_accounts` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}newsletter_archive` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}newsletter_archive` CHANGE `status` `status` TINYINT(1) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}pages` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}pages` CHANGE `mode` `mode` TINYINT(1) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}pages` CHANGE `target` `target` TINYINT(1) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}pages` ADD `root_id` INT(10) UNSIGNED NOT NULL AFTER `block_id`',
	'ALTER TABLE `{pre}pages` ADD `left_id` INT(10) UNSIGNED NOT NULL AFTER `root_id`',
	'ALTER TABLE `{pre}pages` ADD `right_id` INT(10) UNSIGNED NOT NULL AFTER `left_id`',
	'ALTER TABLE `{pre}pages` ADD `display` TINYINT(1) UNSIGNED NOT NULL AFTER `right_id`',
	'UPDATE `{pre}pages` SET display = 1 WHERE block_id != "0"',
	'UPDATE `{pre}pages` SET root_id = id WHERE parent = "0"',
	'ALTER TABLE `{pre}pages` DROP COLUMN `parent`',
	'ALTER TABLE `{pre}pages` DROP COLUMN `sort`',
	'ALTER TABLE `{pre}pages` DROP INDEX `title`',
	'ALTER TABLE `{pre}pages` ADD INDEX `foreign_block_id` (`block_id`)',
	'RENAME TABLE `{pre}pages` TO `{pre}menu_items`',
	'ALTER TABLE `{pre}pages_blocks` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'RENAME TABLE `{pre}pages_blocks` TO `{pre}menu_items_blocks`',
	'ALTER TABLE `{pre}poll_answers` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}poll_answers` CHANGE `poll_id` `poll_id` INT(10) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}poll_answers` ADD INDEX `foreign_poll_id` (`poll_id`)',
	'ALTER TABLE `{pre}poll_question` ADD `multiple` TINYINT(1) UNSIGNED NOT NULL AFTER `question`',
	'ALTER TABLE `{pre}poll_question` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}poll_votes` CHANGE `poll_id` `poll_id` INT(10) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}poll_votes` CHANGE `answer_id` `answer_id` INT(10) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}poll_votes` ADD `user_id` INT(10) UNSIGNED NOT NULL AFTER `answer_id`',
	'ALTER TABLE `{pre}poll_votes` DROP PRIMARY KEY',
	'ALTER TABLE `{pre}poll_votes` ADD INDEX `foreign_poll_id_answer_id_user_id` (`poll_id`, `answer_id`, `user_id`)',
	'CREATE TABLE `{pre}static_pages` ( `id` INT(10) UNSIGNED NOT NULL auto_increment, `start` VARCHAR(14) NOT NULL, `end` VARCHAR(14) NOT NULL, `title` VARCHAR(120) NOT NULL, `text` TEXT NOT NULL, PRIMARY KEY (`id`), FULLTEXT KEY `index` (`title`, `text`)) {engine} {charset};',
	'ALTER TABLE `{pre}users` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}users` CHANGE `access` `access` INT(10) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}users` CHANGE `time_zone` `time_zone` INT(5) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}users` CHANGE `dst` `dst` TINYINT(1) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}users` ADD `login_errors` TINYINT(1) UNSIGNED NOT NULL AFTER `access`',
	'ALTER TABLE `{pre}users` ADD `realname2` VARCHAR(80) NOT NULL AFTER `access`',
	'ALTER TABLE `{pre}users` ADD `gender` VARCHAR(3) NOT NULL AFTER `realname2`',
	'ALTER TABLE `{pre}users` ADD `birthday` VARCHAR(16) NOT NULL AFTER `gender`',
	'ALTER TABLE `{pre}users` ADD `birthday_format` TINYINT(1) UNSIGNED NOT NULL AFTER `birthday`',
	'ALTER TABLE `{pre}users` ADD `icq` VARCHAR(11) NOT NULL AFTER `website`',
	'ALTER TABLE `{pre}users` ADD `msn` VARCHAR(120) NOT NULL AFTER `icq`',
	'ALTER TABLE `{pre}users` ADD `skype` VARCHAR(30) NOT NULL AFTER `msn`',
	'UPDATE {pre}users SET realname2 = CONCAT(realname, \':1\'), mail = CONCAT(mail, \':1\'), website = CONCAT(website, \':1\'), gender = \'1:1\', birthday = \':1\', birthday_format = \'1\', icq = \':1\', msn = \':1\', skype = \':1\'',
	'ALTER TABLE `{pre}users` DROP `realname`',
	'ALTER TABLE `{pre}users` CHANGE `realname2` `realname` VARCHAR(80) NOT NULL',
);

// Änderungen am DB Schema vornehmen
if (count($queries) > 0) {
	$db = new db();
	$handle = $db->connect(CONFIG_DB_HOST, CONFIG_DB_NAME, CONFIG_DB_USER, CONFIG_DB_PASSWORD);
	if ($handle !== true) {
		exit($handle);
	}

	print "Aktualisierung der Datenbank:\n\n";
	$bool = null;

	$engine = 'ENGINE=MyISAM';
	$charset = 'CHARACTER SET `utf8` COLLATE `utf8_general_ci`';

	foreach ($queries as $row) {
		$row = str_replace(array('{pre}', '{engine}', '{charset}'), array(CONFIG_DB_PRE, $engine, $charset), $row);
		$bool = $db->query($row, 3);
		if ($bool === null && defined('DEBUG') && DEBUG) {
			print "\n";
		}
	}

	// Statische Seiten in extra Tabelle auslagern
	$pages = $db->select('id, start, end, title, text, block_id', 'menu_items', 'mode = "1"');
	$c_pages = count($pages);

	if ($c_pages > 0) {
		for ($i = 0; $i < $c_pages; ++$i) {
			$insert_values = array(
				'start' => $pages[$i]['start'],
				'end' => $pages[$i]['end'],
				'title' => $pages[$i]['title'],
				'text' => $pages[$i]['text'],
			);
			$db->insert('static_pages', $insert_values);
			$last_id = $db->select('LAST_INSERT_ID() AS id', 'static_pages');
			$db->update('menu_items', array('uri' => 'static_pages/list/id_' . $last_id[0]['id']), 'id = "' . $pages[$i]['id'] . '"');
		}
		$db->query('ALTER TABLE `' . CONFIG_DB_PRE . 'menu_items` DROP `text`', 3);
	}

	print "\n" . ($bool ? 'Die Datenbank wurde erfolgreich aktualisiert.' : 'Mindestens eine Datenbankänderung konnte nicht durchgeführt werden.') . "\n";
	print "\n----------------------------\n\n";
}

// Konfigurationsdatei aktualisieren
$config = array(
	'date_dst' => CONFIG_DST,
	'date_format' => CONFIG_DATE,
	'date_time_zone' => CONFIG_TIME_ZONE,
	'db_host' => CONFIG_DB_HOST,
	'db_name' => CONFIG_DB_NAME,
	'db_pre' => CONFIG_DB_PRE,
	'db_password' => CONFIG_DB_PWD,
	'db_user' => CONFIG_DB_USER,
	'design' => CONFIG_DESIGN,
	'entries' => CONFIG_ENTRIES,
	'flood' => CONFIG_FLOOD,
	'homepage' => CONFIG_HOMEPAGE,
	'lang' => CONFIG_LANG,
	'maintenance_mode' => CONFIG_MAINTENANCE,
	'maintenance_message' => CONFIG_MAINTENANCE_MSG,
	'seo_meta_description' => CONFIG_META_DESCRIPTION,
	'seo_meta_keywords' => CONFIG_META_KEYWORDS,
	'seo_mod_rewrite' => CONFIG_SEF,
	'seo_title' => CONFIG_TITLE,
	'version' => NEW_VERSION,
	'wysiwyg' => 'fckeditor'
);

print config::system($config) ? 'Konfigurationsdatei erfolgreich aktualisiert.' : 'Konfigurationsdatei konnte nicht aktualisiert werden.';

// Cache leeren
cache::purge();
?>