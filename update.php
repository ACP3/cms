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
	'ALTER TABLE `{pre}emoticons` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}files` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}files` CHANGE `category_id` `category_id` INT(10) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}gallery` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}gallery_pictures` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}gallery_pictures` CHANGE `gallery_id` `gallery_id` INT(10) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}guestbook` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}guestbook` CHANGE `user_id` `user_id` INT(10) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}news` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}news` CHANGE `category_id` `category_id` INT(10) UNSIGNED NOT NULL',
	'RENAME TABLE `{pre}nnewsletter_accounts` TO `{pre}newsletter_accounts`',
	'ALTER TABLE `{pre}newsletter_accounts` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}newsletter_archive` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}pages` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}pages` DROP COLUMN `parent`',
	'ALTER TABLE `{pre}pages` DROP COLUMN `sort`',
	'ALTER TABLE `{pre}pages` ADD `left_id` INT(10) UNSIGNED NOT NULL AFTER `block_id`',
	'ALTER TABLE `{pre}pages` ADD `right_id` INT(10) UNSIGNED NOT NULL AFTER `left_id`',
	'RENAME TABLE `{pre}pages` TO `{pre}menu_items`',
	'ALTER TABLE `{pre}pages_blocks` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'RENAME TABLE `{pre}pages_blocks` TO `{pre}menu_items_blocks`',
	'ALTER TABLE `{pre}poll_answers` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}poll_answers` CHANGE `poll_id` `poll_id` INT(10) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}poll_question` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
	'ALTER TABLE `{pre}poll_votes` CHANGE `poll_id` `poll_id` INT(10) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}poll_votes` CHANGE `answer_id` `answer_id` INT(10) UNSIGNED NOT NULL',
	'ALTER TABLE `{pre}poll_votes` ADD `user_id` INT(10) UNSIGNED NOT NULL AFTER `answer_id`',
	'ALTER TABLE `{pre}poll_votes` DROP PRIMARY KEY',
	'ALTER TABLE `{pre}poll_votes` ADD INDEX (`poll_id`, `answer_id`, `user_id`)',
	'ALTER TABLE `{pre}users` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT',
);

if (count($queries) > 0) {
	$db = new db();

	if (version_compare(CONFIG_DB_TYPE == 'mysqli' ?  mysqli_get_client_info() : mysql_get_client_info(), '4.1', '>=')) {
		$engine = 'ENGINE=MyISAM';
		$charset = 'CHARACTER SET `utf8` COLLATE `utf8_general_ci`';
	} else {
		$engine = 'TYPE=MyISAM';
		$charset = 'CHARSET=utf-8';
	}

	print "Aktualisierung der Datenbank:\n\n";
	$bool = false;

	foreach ($queries as $row) {
		$row = str_replace(array('{pre}', '{engine}', '{charset}'), array(CONFIG_DB_PRE, $engine, $charset), $row);
		$bool = $db->query($row, 3);
		if (!$bool && defined('DEBUG') && DEBUG) {
			print "\n";
		}
	}
	print "\n" . ($bool ? 'Die Datenbank wurde erfolgreich aktualisiert.' : 'Mindestens eine Datenbankänderung konnte nicht durchgeführt werden.') . "\n";
	print "\n----------------------------\n\n";
}

// Konfigurationsdatei aktualisieren
$config = array(
	'date' => CONFIG_DATE,
	'db_host' => CONFIG_DB_HOST,
	'db_name' => CONFIG_DB_NAME,
	'db_pre' => CONFIG_DB_PRE,
	'db_pwd' => CONFIG_DB_PWD,
	'db_type' => CONFIG_DB_TYPE,
	'db_user' => CONFIG_DB_USER,
	'design' => CONFIG_DESIGN,
	'dst' => CONFIG_DST,
	'entries' => CONFIG_ENTRIES,
	'flood' => CONFIG_FLOOD,
	'homepage' => CONFIG_HOMEPAGE,
	'lang' => CONFIG_LANG,
	'maintenance' => CONFIG_MAINTENANCE,
	'maintenance_msg' => CONFIG_MAINTENANCE_MSG,
	'meta_description' => CONFIG_META_DESCRIPTION,
	'meta_keywords' => CONFIG_META_KEYWORDS,
	'sef' => CONFIG_SEF,
	'time_zone' => CONFIG_TIME_ZONE,
	'title' => CONFIG_TITLE,
	'version' => NEW_VERSION,
	'wysiwyg' => 'fckeditor'
);

print config::system($config) ? 'Konfigurationsdatei erfolgreich aktualisiert.' : 'Konfigurationsdatei konnte nicht aktualisiert werden.';

// Cache leeren
cache::purge();
?>