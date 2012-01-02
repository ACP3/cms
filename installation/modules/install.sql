CREATE TABLE `{pre}acl_privileges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) {engine};

CREATE TABLE`{pre}acl_resources` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  `privilege_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `path` (`path`)
) {engine};

CREATE TABLE`{pre}acl_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `left_id` int(10) unsigned NOT NULL,
  `right_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) {engine};

CREATE TABLE`{pre}acl_role_privileges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `privilege_id` int(10) unsigned NOT NULL,
  `value` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_id` (`role_id`,`privilege_id`)
) {engine};

CREATE TABLE`{pre}acl_user_roles` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`)
) {engine};

CREATE TABLE `{pre}categories` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(120) NOT NULL,
	`picture` VARCHAR(120) NOT NULL,
	`description` VARCHAR(120) NOT NULL,
	`module` VARCHAR(120) NOT NULL,
	PRIMARY KEY (`id`)
) {engine};

CREATE TABLE `{pre}comments` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`ip` VARCHAR(40) NOT NULL,
	`date` VARCHAR(14) NOT NULL,
	`name` VARCHAR(20) NOT NULL,
	`user_id` INT(10) UNSIGNED NOT NULL,
	`message` TEXT NOT NULL,
	`module` VARCHAR(120) NOT NULL,
	`entry_id` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`), INDEX `foreign_entry_id` (`entry_id`)
) {engine};

CREATE TABLE `{pre}emoticons` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`code` VARCHAR(10) NOT NULL,
	`description` VARCHAR(15) NOT NULL,
	`img` VARCHAR(40) NOT NULL,
	PRIMARY KEY (`id`)
) {engine};

CREATE TABLE `{pre}files` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`start` VARCHAR(14) NOT NULL,
	`end` VARCHAR(14) NOT NULL,
	`category_id` INT(10) UNSIGNED NOT NULL,
	`file` VARCHAR(120) NOT NULL,
	`size` VARCHAR(20) NOT NULL,
	`link_title` VARCHAR(120) NOT NULL,
	`text` TEXT NOT NULL,
	`comments` TINYINT(1) UNSIGNED NOT NULL,
	`user_id` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`id`), FULLTEXT KEY `index` (`link_title`, `text`), INDEX `foreign_category_id` (`category_id`)
) {engine};

CREATE TABLE `{pre}gallery` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`start` VARCHAR(14) NOT NULL,
	`end` VARCHAR(14) NOT NULL,
	`name` VARCHAR(120) NOT NULL,
	`user_id` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`id`)
) {engine};

CREATE TABLE `{pre}gallery_pictures` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`pic` INT(10) UNSIGNED NOT NULL,
	`gallery_id` INT(10) UNSIGNED NOT NULL,
	`file` VARCHAR(120) NOT NULL,
	`description` TEXT NOT NULL,
	`comments` TINYINT(1) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`), INDEX `foreign_gallery_id` (`gallery_id`)
) {engine};

CREATE TABLE `{pre}guestbook` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`ip` VARCHAR(40) NOT NULL,
	`date` VARCHAR(14) NOT NULL,
	`name` VARCHAR(20) NOT NULL,
	`user_id` INT(10) UNSIGNED NOT NULL,
	`message` TEXT NOT NULL,
	`website` VARCHAR(120) NOT NULL,
	`mail` VARCHAR(120) NOT NULL,
	`active` TINYINT(1) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`), INDEX `foreign_user_id` (`user_id`)
) {engine};

CREATE TABLE `{pre}menu_items` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`start` VARCHAR(14) NOT NULL,
	`end` VARCHAR(14) NOT NULL,
	`mode` TINYINT(1) UNSIGNED NOT NULL,
	`block_id` INT(10) UNSIGNED NOT NULL,
	`root_id` INT(10) UNSIGNED NOT NULL,
	`left_id` INT(10) UNSIGNED NOT NULL,
	`right_id` INT(10) UNSIGNED NOT NULL,
	`display` TINYINT(1) UNSIGNED NOT NULL,
	`title` VARCHAR(120) NOT NULL,
	`uri` VARCHAR(120) NOT NULL,
	`target` TINYINT(1) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`), INDEX `foreign_block_id` (`block_id`)
) {engine};

CREATE TABLE `{pre}menu_items_blocks` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`index_name` VARCHAR(10) NOT NULL,
	`title` VARCHAR(120) NOT NULL,
	PRIMARY KEY (`id`)
) {engine};

CREATE TABLE `{pre}modules` (
	`name` varchar(100) NOT NULL,
	`active` tinyint(1) unsigned NOT NULL,
	PRIMARY KEY (`name`)
) {engine};

CREATE TABLE `{pre}news` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`start` VARCHAR(14) NOT NULL,
	`end` VARCHAR(14) NOT NULL,
	`headline` VARCHAR(120) NOT NULL,
	`text` TEXT NOT NULL,
	`readmore` TINYINT(1) UNSIGNED NOT NULL,
	`comments` TINYINT(1) UNSIGNED NOT NULL,
	`category_id` INT(10) UNSIGNED NOT NULL,
	`uri` VARCHAR(120) NOT NULL,
	`target` TINYINT(1) UNSIGNED NOT NULL,
	`link_title` VARCHAR(120) NOT NULL,
	`user_id` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`id`), FULLTEXT KEY `index` (`headline`,`text`), INDEX `foreign_category_id` (`category_id`)
) {engine};

CREATE TABLE `{pre}newsletter_accounts` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`mail` VARCHAR(120) NOT NULL,
	`hash` VARCHAR(32) NOT NULL,
	PRIMARY KEY (`id`)
) {engine};

CREATE TABLE `{pre}newsletter_archive` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`date` VARCHAR(14) NOT NULL,
	`subject` VARCHAR(120) NOT NULL,
	`text` TEXT NOT NULL,
	`status` TINYINT(1) UNSIGNED NOT NULL,
	`user_id` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`id`)
) {engine};

CREATE TABLE `{pre}poll_answers` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`text` VARCHAR(120) NOT NULL,
	`poll_id` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`), INDEX `foreign_poll_id` (`poll_id`)
) {engine};

CREATE TABLE `{pre}polls` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`start` VARCHAR(14) NOT NULL,
	`end` VARCHAR(14) NOT NULL,
	`question` VARCHAR(120) NOT NULL,
	`multiple` TINYINT(1) UNSIGNED NOT NULL,
	`user_id` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`id`)
) {engine};

CREATE TABLE `{pre}poll_votes` (
	`poll_id` INT(10) UNSIGNED NOT NULL,
	`answer_id` INT(10) UNSIGNED NOT NULL,
	`user_id` INT(10) UNSIGNED NOT NULL,
	`ip` VARCHAR(40) NOT NULL,
	`time` VARCHAR(14) NOT NULL,
	INDEX (`poll_id`, `answer_id`, `user_id`)
) {engine};

CREATE TABLE `{pre}seo` (
	`uri` varchar(255) NOT NULL,
	`alias` varchar(100) NOT NULL,
	`keywords` varchar(255) NOT NULL,
	`description` varchar(255) NOT NULL,
	PRIMARY KEY (`uri`),
	UNIQUE KEY `alias` (`alias`)
) {engine};

CREATE TABLE `{pre}settings` (
 `id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
 `module` VARCHAR(40) NOT NULL,
 `name` VARCHAR(40) NOT NULL,
 `value` TEXT NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `module` (`module`,`name`)
) {engine};

CREATE TABLE `{pre}static_pages` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`start` VARCHAR(14) NOT NULL,
	`end` VARCHAR(14) NOT NULL,
	`title` VARCHAR(120) NOT NULL,
	`text` TEXT NOT NULL,
	`user_id` INT UNSIGNED NOT NULL,
	PRIMARY KEY (`id`), FULLTEXT KEY `index` (`title`, `text`)
) {engine};

CREATE TABLE `{pre}users` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`nickname` VARCHAR(30) NOT NULL,
	`pwd` VARCHAR(53) NOT NULL,
	`login_errors` TINYINT(1) UNSIGNED NOT NULL,
	`realname` VARCHAR(80) NOT NULL,
	`gender` VARCHAR(3) NOT NULL,
	`birthday` VARCHAR(16) NOT NULL,
	`birthday_format` TINYINT(1) UNSIGNED NOT NULL,
	`mail` VARCHAR(120) NOT NULL,
	`website` VARCHAR(120) NOT NULL,
	`icq` VARCHAR(11) NOT NULL,
	`msn` VARCHAR(120) NOT NULL,
	`skype` VARCHAR(30) NOT NULL,
	`date_format_long` VARCHAR(30) NOT NULL,
	`date_format_short` VARCHAR(30) NOT NULL,
	`time_zone` int(5) UNSIGNED NOT NULL,
	`dst` TINYINT(1) UNSIGNED NOT NULL,
	`language` VARCHAR(10) NOT NULL,
	`entries` TINYINT(2) UNSIGNED NOT NULL,
	`draft` TEXT NOT NULL,
	PRIMARY KEY (`id`)
) {engine};

INSERT INTO `{pre}categories` VALUES ('', 'Erste Kategorie', '', 'Dies ist die erste Kategorie', 'news');
INSERT INTO `{pre}emoticons` VALUES ('', ':D', 'Very Happy', '1.gif'), ('', ':)', 'Smile', '2.gif'), ('', ':(', 'Sad', '3.gif'), ('', ':o', 'Surprised', '4.gif'), ('', ':shocked:', 'Shocked', '5.gif'), ('', ':?', 'Confused', '6.gif'), ('', ':8)', 'Cool', '7.gif'), ('', ':lol:', 'Laughing', '8.gif'), ('', ':x', 'Mad', '9.gif'), ('', ':P', 'Razz', '10.gif'), ('', ':oops:', 'Embarassed', '11.gif'), ('', ':cry:', 'Crying', '12.gif'), ('', ':evil:', 'Evil', '13.gif'), ('', ':twisted:', 'Twisted Evil', '14.gif'), ('', ':roll:', 'Rolling Eyes', '15.gif'), ('', ':wink:', 'Wink', '16.gif'), ('', ':!:', 'Exclamation', '17.gif'), ('', ':?:', 'Question', '18.gif'), ('', ':idea:', 'Idea', '19.gif'), ('', ':arrow:', 'Arrow', '20.gif'), ('', ':|', 'Neutral', '21.gif'), ('', ':mrgreen:', 'Mr. Green', '22.gif');
INSERT INTO `{pre}settings` VALUES (1, 'categories', 'width', '100');
INSERT INTO `{pre}settings` VALUES (2, 'categories', 'height', '50'), (3, 'categories', 'filesize', '40960');
INSERT INTO `{pre}settings` VALUES (4, 'comments', 'dateformat', 'long');
INSERT INTO `{pre}settings` VALUES (5, 'contact', 'mail', ''), (6, 'contact', 'address', ''), (7, 'contact', 'telephone', ''), (8, 'contact', 'fax', ''), (9, 'contact', 'disclaimer', ''), (10, 'contact', 'layout', '<div class="imprint"><dl><dt>{address_lang}</dt><dd>{address_value}</dd></dl><dl><dt>{email_lang}</dt><dd>{email_value}</dd></dl><dl><dt>{telephone_lang}</dt><dd>{telephone_value}</dd></dl><dl><dt>{fax_lang}</dt><dd>{fax_value}</dd></dl><dl><dt>{disclaimer_lang}</dt><dd>{disclaimer_value}</dd></dl></div>');
INSERT INTO `{pre}settings` VALUES (11, 'emoticons', 'width', '32'), (12, 'emoticons', 'height', '32'), (13, 'emoticons', 'filesize', '10240');
INSERT INTO `{pre}settings` VALUES (14, 'files', 'comments', '1'), (15, 'files', 'dateformat', 'long'), (16, 'files', 'sidebar', '5');
INSERT INTO `{pre}settings` VALUES (17, 'gallery', 'width', '640'), (18, 'gallery', 'height', '480'), (19, 'gallery', 'thumbwidth', '160'), (20, 'gallery', 'thumbheight', '120'), (21, 'gallery', 'maxwidth', '1024'), (22, 'gallery', 'maxheight', '768'), (23, 'gallery', 'filesize', '20971520'), (24, 'gallery', 'colorbox', '1'), (25, 'gallery', 'comments', '1'), (26, 'gallery', 'dateformat', 'long'), (27, 'gallery', 'sidebar', '5');
INSERT INTO `{pre}settings` VALUES (28, 'guestbook', 'dateformat', 'long'), (29, 'guestbook', 'notify', '0'), (30, 'guestbook', 'notify_email', ''), (31, 'guestbook', 'emoticons', '1'), (32, 'guestbook', 'newsletter_integration', '0');
INSERT INTO `{pre}settings` VALUES (33, 'news', 'comments', '1'), (34, 'news', 'dateformat', 'long'), (35, 'news', 'readmore', '1'), (36, 'news', 'readmore_chars', '350'), (37, 'news', 'sidebar', '5');
INSERT INTO `{pre}settings` VALUES (38, 'newsletter', 'mail', ''), (39, 'newsletter', 'mailsig', '');
INSERT INTO `{pre}settings` VALUES (40, 'users', 'language_override', '1'), (41, 'users', 'entries_override', '1');
INSERT INTO `{pre}modules` (`name`, `active`) VALUES ('access', 1), ('acp', 1), ('captcha', 1), ('categories', 1), ('comments', 1), ('contact', 1), ('emoticons', 1), ('errors', 1), ('feeds', 1), ('files', 1), ('gallery', 1), ('guestbook', 1), ('menu_items', 1), ('news', 1), ('newsletter', 1), ('polls', 1), ('search', 1), ('static_pages', 1), ('system', 1), ('users', 1);
INSERT INTO `{pre}acl_privileges` (`id`, `key`, `name`) VALUES (1, 'view', ''), (2, 'create', ''), (3, 'admin_view', ''), (4, 'admin_create', ''), (5, 'admin_edit', ''), (6, 'admin_delete', ''), (7, 'admin_settings', '');
INSERT INTO `{pre}acl_resources` (`id`, `path`, `privilege_id`) VALUES
(3, 'access/adm_list/', 3),
(4, 'access/create/', 4),
(5, 'access/delete/', 6),
(6, 'access/edit/', 5),
(7, 'access/functions/', 1),
(8, 'acp/adm_list/', 3),
(9, 'captcha/image/', 1),
(10, 'categories/adm_list/', 3),
(11, 'categories/create/', 4),
(12, 'categories/delete/', 6),
(13, 'categories/edit/', 5),
(14, 'categories/functions/', 1),
(15, 'categories/settings/', 7),
(16, 'comments/adm_list/', 3),
(17, 'comments/create/', 2),
(18, 'comments/delete_comments/', 1),
(19, 'comments/delete_comments_per_module/', 1),
(20, 'comments/edit/', 5),
(21, 'comments/functions/', 1),
(22, 'comments/settings/', 7),
(23, 'contact/adm_list/', 3),
(24, 'contact/imprint/', 1),
(25, 'contact/list/', 1),
(26, 'emoticons/adm_list/', 3),
(27, 'emoticons/create/', 4),
(28, 'emoticons/delete/', 6),
(29, 'emoticons/edit/', 5),
(30, 'emoticons/functions/', 1),
(31, 'emoticons/settings/', 7),
(32, 'errors/403/', 1),
(33, 'errors/404/', 1),
(34, 'feeds/list/', 1),
(35, 'files/adm_list/', 3),
(36, 'files/create/', 4),
(37, 'files/delete/', 6),
(38, 'files/details/', 1),
(39, 'files/edit/', 5),
(40, 'files/files/', 1),
(41, 'files/functions/', 1),
(42, 'files/list/', 1),
(43, 'files/settings/', 7),
(44, 'files/sidebar/', 1),
(45, 'gallery/add_picture/', 1),
(46, 'gallery/adm_list/', 3),
(47, 'gallery/create/', 4),
(48, 'gallery/delete_gallery/', 1),
(49, 'gallery/delete_picture/', 1),
(50, 'gallery/details/', 1),
(51, 'gallery/edit_gallery/', 1),
(52, 'gallery/edit_picture/', 1),
(53, 'gallery/functions/', 1),
(54, 'gallery/image/', 1),
(55, 'gallery/list/', 1),
(56, 'gallery/order/', 1),
(57, 'gallery/pics/', 1),
(58, 'gallery/settings/', 7),
(59, 'gallery/sidebar/', 1),
(60, 'guestbook/adm_list/', 3),
(61, 'guestbook/create/', 4),
(62, 'guestbook/delete/', 6),
(63, 'guestbook/edit/', 5),
(64, 'guestbook/list/', 1),
(65, 'guestbook/settings/', 7),
(66, 'menu_items/adm_list/', 3),
(67, 'menu_items/adm_list_blocks/', 1),
(68, 'menu_items/create/', 4),
(69, 'menu_items/create_block/', 1),
(70, 'menu_items/delete/', 6),
(71, 'menu_items/delete_blocks/', 1),
(72, 'menu_items/edit/', 5),
(73, 'menu_items/edit_block/', 1),
(74, 'menu_items/functions/', 1),
(75, 'menu_items/order/', 1),
(76, 'news/adm_list/', 3),
(77, 'news/create/', 4),
(78, 'news/delete/', 6),
(79, 'news/details/', 1),
(80, 'news/edit/', 5),
(81, 'news/functions/', 1),
(82, 'news/list/', 1),
(83, 'news/settings/', 7),
(84, 'news/sidebar/', 1),
(85, 'newsletter/activate/', 1),
(86, 'newsletter/adm_activate/', 1),
(87, 'newsletter/adm_list/', 3),
(88, 'newsletter/adm_list_archive/', 1),
(89, 'newsletter/compose/', 1),
(90, 'newsletter/create/', 4),
(91, 'newsletter/delete/', 6),
(92, 'newsletter/delete_archive/', 1),
(93, 'newsletter/edit_archive/', 1),
(94, 'newsletter/functions/', 1),
(95, 'newsletter/send/', 1),
(96, 'newsletter/settings/', 7),
(97, 'polls/adm_list/', 3),
(98, 'polls/create/', 4),
(99, 'polls/delete/', 6),
(100, 'polls/edit/', 5),
(101, 'polls/list/', 1),
(102, 'polls/result/', 1),
(103, 'polls/sidebar/', 1),
(104, 'polls/vote/', 1),
(105, 'search/list/', 1),
(106, 'static_pages/adm_list/', 3),
(107, 'static_pages/create/', 4),
(108, 'static_pages/delete/', 6),
(109, 'static_pages/edit/', 5),
(110, 'static_pages/functions/', 1),
(111, 'static_pages/list/', 1),
(112, 'system/adm_list/', 3),
(113, 'system/configuration/', 1),
(114, 'system/designs/', 1),
(115, 'system/extensions/', 1),
(116, 'system/languages/', 1),
(117, 'system/maintenance/', 1),
(118, 'system/modules/', 1),
(120, 'system/sql_export/', 1),
(121, 'system/sql_import/', 1),
(122, 'system/sql_optimisation/', 1),
(123, 'system/update_check/', 1),
(124, 'users/adm_list/', 3),
(125, 'users/create/', 4),
(126, 'users/delete/', 6),
(127, 'users/edit/', 5),
(128, 'users/edit_profile/', 1),
(129, 'users/edit_settings/', 7),
(130, 'users/forgot_pwd/', 1),
(131, 'users/functions/', 1),
(132, 'users/home/', 1),
(133, 'users/list/', 1),
(134, 'users/login/', 1),
(135, 'users/logout/', 1),
(136, 'users/register/', 1),
(137, 'users/settings/', 7),
(138, 'users/sidebar/', 1),
(139, 'users/view_profile/', 1),
(140, 'news/extensions/feeds/', 1),
(141, 'news/extensions/search/', 1),
(142, 'files/extensions/feeds/', 1),
(143, 'files/extensions/search/', 1),
(144, 'static_pages/extensions/search/', 1);
(145, 'access/order/', 5);
INSERT INTO `{pre}acl_roles` (`id`, `name`, `left_id`, `right_id`) VALUES (1, 'Gast', 1, 8), (2, 'Mitglied', 2, 7), (3, 'Autor', 3, 6), (4, 'Administrator', 4, 5);
INSERT INTO `{pre}acl_role_privileges` (`id`, `role_id`, `privilege_id`, `value`) VALUES (1, 1, 1, 1), (2, 1, 2, 1), (3, 1, 3, 0), (4, 1, 4, 0), (5, 1, 5, 0), (6, 1, 6, 0), (7, 1, 7, 0), (8, 2, 1, 2), (9, 2, 2, 2), (10, 2, 3, 2), (11, 2, 4, 2), (12, 2, 5, 2), (13, 2, 6, 2), (14, 2, 7, 2), (15, 3, 1, 2), (16, 3, 2, 2), (17, 3, 3, 1), (18, 3, 4, 1), (19, 3, 5, 1), (20, 3, 6, 1), (21, 3, 7, 2), (22, 4, 1, 2), (23, 4, 2, 2), (24, 4, 3, 2), (25, 4, 4, 2), (26, 4, 5, 2), (27, 4, 6, 2), (28, 4, 7, 1);
INSERT INTO `{pre}acl_user_roles` (`user_id`, `role_id`) VALUES (0, 1), (1, 4);