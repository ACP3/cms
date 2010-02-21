CREATE TABLE `{pre}access` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`name` VARCHAR(120) NOT NULL,
	`modules` TEXT NOT NULL
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
	PRIMARY KEY (`id`), FULLTEXT KEY `index` (`link_title`, `text`), INDEX `foreign_category_id` (`category_id`)
) {engine};

CREATE TABLE `{pre}gallery` ( 
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`start` VARCHAR(14) NOT NULL,
	`end` VARCHAR(14) NOT NULL,
	`name` VARCHAR(120) NOT NULL,
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
	PRIMARY KEY (`id`)
) {engine};

CREATE TABLE `{pre}poll_answers` ( 
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`text` VARCHAR(120) NOT NULL,
	`poll_id` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`), INDEX `foreign_poll_id` (`poll_id`)
) {engine};

CREATE TABLE `{pre}poll_question` ( 
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`start` VARCHAR(14) NOT NULL,
	`end` VARCHAR(14) NOT NULL,
	`question` VARCHAR(120) NOT NULL,
	`multiple` TINYINT(1) UNSIGNED NOT NULL,
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

CREATE TABLE `{pre}static_pages` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`start` VARCHAR(14) NOT NULL,
	`end` VARCHAR(14) NOT NULL,
	`title` VARCHAR(120) NOT NULL,
	`text` TEXT NOT NULL,
	PRIMARY KEY (`id`), FULLTEXT KEY `index` (`title`, `text`)
) {engine};

CREATE TABLE `{pre}users` ( 
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`nickname` VARCHAR(30) NOT NULL,
	`pwd` VARCHAR(53) NOT NULL,
	`access` INT(10) UNSIGNED NOT NULL,
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

INSERT INTO `{pre}access` VALUES ('1', 'Administrator', 'users:16,feeds:16,files:16,emoticons:16,errors:16,gallery:16,guestbook:16,categories:16,comments:16,contact:16,menu_items:16,news:16,newsletter:16,static_pages:16,search:16,system:16,polls:16,access:16,acp:16,captcha:16');
INSERT INTO `{pre}access` VALUES ('2', 'Besucher', 'users:1,feeds:1,files:1,emoticons:1,errors:1,gallery:1,guestbook:3,categories:1,comments:3,contact:1,menu_items:1,news:1,newsletter:1,static_pages:1,search:1,system:0,polls:1,access:0,acp:0,captcha:1');
INSERT INTO `{pre}access` VALUES ('3', 'Benutzer', 'users:1,feeds:1,files:1,emoticons:1,errors:1,gallery:1,guestbook:3,categories:1,comments:3,contact:1,menu_items:1,news:1,newsletter:1,static_pages:1,search:1,system:0,polls:1,access:0,acp:0,captcha:1');
INSERT INTO `{pre}categories` VALUES ('', 'Erste Kategorie', '', 'Dies ist die erste Kategorie', 'news');
INSERT INTO `{pre}emoticons` VALUES ('', ':D', 'Very Happy', '1.gif'), ('', ':)', 'Smile', '2.gif'), ('', ':(', 'Sad', '3.gif'), ('', ':o', 'Surprised', '4.gif'), ('', ':shocked:', 'Shocked', '5.gif'), ('', ':?', 'Confused', '6.gif'), ('', ':8)', 'Cool', '7.gif'), ('', ':lol:', 'Laughing', '8.gif'), ('', ':x', 'Mad', '9.gif'), ('', ':P', 'Razz', '10.gif'), ('', ':oops:', 'Embarassed', '11.gif'), ('', ':cry:', 'Crying', '12.gif'), ('', ':evil:', 'Evil', '13.gif'), ('', ':twisted:', 'Twisted Evil', '14.gif'), ('', ':roll:', 'Rolling Eyes', '15.gif'), ('', ':wink:', 'Wink', '16.gif'), ('', ':!:', 'Exclamation', '17.gif'), ('', ':?:', 'Question', '18.gif'), ('', ':idea:', 'Idea', '19.gif'), ('', ':arrow:', 'Arrow', '20.gif'), ('', ':|', 'Neutral', '21.gif'), ('', ':mrgreen:', 'Mr. Green', '22.gif');