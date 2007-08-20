CREATE TABLE `{pre}access` ( `id` int(11) NOT NULL auto_increment, `name` varchar(120) NOT NULL, `mods` text NOT NULL, PRIMARY KEY (`id`)) {engine} ;

CREATE TABLE `{pre}categories` ( `id` int(11) NOT NULL auto_increment, `name` varchar(120) NOT NULL, `description` varchar(120) NOT NULL, `module` varchar(120) NOT NULL, PRIMARY KEY (`id`)) {engine} ;

CREATE TABLE `{pre}comments` ( `id` int(11) NOT NULL auto_increment, `ip` varchar(40) NOT NULL, `date` varchar(14) NOT NULL, `name` varchar(20) NOT NULL, `message` text NOT NULL, `module` varchar(120) NOT NULL, `entry_id` int(11) NOT NULL, PRIMARY KEY (`id`)) {engine} ;

CREATE TABLE `{pre}dl` ( `id` int(11) NOT NULL auto_increment, `start` varchar(14) NOT NULL, `end` varchar(14) NOT NULL, `cat` int(11) NOT NULL, `file` varchar(120) NOT NULL, `size` float NOT NULL, `link_title` varchar(120) NOT NULL, `text` text NOT NULL, PRIMARY KEY (`id`), FULLTEXT KEY `file` (`link_title`,`text`)) {engine} ;

CREATE TABLE `{pre}emoticons` ( `id` int(11) NOT NULL auto_increment, `code` varchar(10) NOT NULL, `description` varchar(15) NOT NULL, `img` varchar(40) NOT NULL, PRIMARY KEY (`id`)) {engine} ;

CREATE TABLE `{pre}gallery` ( `id` int(11) NOT NULL auto_increment, `start` varchar(14) NOT NULL, `end` varchar(14) NOT NULL, `name` varchar(120) NOT NULL, PRIMARY KEY (`id`)) {engine} ;

CREATE TABLE `{pre}galpics` ( `id` int(11) NOT NULL auto_increment, `pic` int(11) NOT NULL, `gallery` int(11) NOT NULL, `file` varchar(120) NOT NULL, `description` text NOT NULL, PRIMARY KEY (`id`)) {engine} ;

CREATE TABLE `{pre}gb` ( `id` int(11) NOT NULL auto_increment, `ip` varchar(40) NOT NULL, `date` varchar(14) NOT NULL, `name` varchar(20) NOT NULL, `message` text NOT NULL, `website` varchar(120) NOT NULL, `mail` varchar(120) NOT NULL, PRIMARY KEY (`id`)) {engine} ;

CREATE TABLE `{pre}news` ( `id` int(11) NOT NULL auto_increment, `start` varchar(14) NOT NULL, `end` varchar(14) NOT NULL, `headline` varchar(120) NOT NULL, `text` text NOT NULL, `cat` int(11) NOT NULL, `uri` varchar(120) NOT NULL, `target` tinyint(1) NOT NULL, `link_title` varchar(120) NOT NULL, PRIMARY KEY (`id`), FULLTEXT KEY `headline` (`headline`,`text`)) {engine} ;

CREATE TABLE `{pre}nl_accounts` ( `id` int(11) NOT NULL auto_increment, `mail` varchar(120) NOT NULL, `hash` varchar(32) NOT NULL, PRIMARY KEY (`id`)) {engine} ;

CREATE TABLE `{pre}pages` ( `id` int(11) NOT NULL auto_increment, `start` varchar(14) NOT NULL, `end` varchar(14) NOT NULL, `mode` tinyint(1) NOT NULL, `parent` int(11) NOT NULL, `block_id` int(11) NOT NULL, `sort` tinyint(2) NOT NULL, `title` varchar(120) NOT NULL, `uri` varchar(120) NOT NULL, `target` tinyint(1) NOT NULL, `text` longtext NOT NULL, PRIMARY KEY (`id`), FULLTEXT KEY `title` (`title`,`text`)) {engine} ;

CREATE TABLE `{pre}pages_blocks` (`id` int(11) NOT NULL auto_increment, `index_name` varchar(10) NOT NULL, `title` varchar(120) NOT NULL, PRIMARY KEY (`id`)) {engine} ;

CREATE TABLE `{pre}poll_answers` ( `id` int(11) NOT NULL auto_increment, `text` varchar(120) NOT NULL, `poll_id` int(11) NOT NULL, PRIMARY KEY (`id`)) {engine} ;

CREATE TABLE `{pre}poll_question` ( `id` int(11) NOT NULL auto_increment, `start` varchar(14) NOT NULL, `end` varchar(14) NOT NULL, `question` varchar(120) NOT NULL, PRIMARY KEY (`id`)) {engine} ;

CREATE TABLE `{pre}poll_votes` ( `poll_id` int(11) NOT NULL, `answer_id` int(11) NOT NULL, `ip` varchar(40) NOT NULL, `time` varchar(14) NOT NULL, PRIMARY KEY (`answer_id`)) {engine} ;

CREATE TABLE `{pre}users` ( `id` int(11) NOT NULL auto_increment, `name` varchar(120) NOT NULL, `pwd` varchar(53) NOT NULL, `access` int(11) NOT NULL, `mail` varchar(120) NOT NULL, PRIMARY KEY (`id`)) {engine} ;

INSERT INTO `{pre}access` VALUES ('', 'Administrator', 'users:2,files:2,emoticons:2,errors:2,gallery:2,gb:2,home:2,categories:2,comments:2,contact:2,pages:2,news:2,newsletter:2,search:2,system:2,polls:2,access:2');

INSERT INTO `{pre}access` VALUES ('', 'Besucher', 'users:1,files:1,emoticons:1,errors:1,gallery:1,gb:1,home:0,categories:1,comments:1,contact:1,pages:1,news:1,newsletter:1,search:1,system:0,polls:1,access:0');

INSERT INTO `{pre}categories` VALUES ('', 'Erste Kategorie', 'Dies ist die erste Kategorie', 'news');

INSERT INTO `{pre}emoticons` VALUES ('', ':D', 'Very Happy', '1.gif'), ('', ':)', 'Smile', '2.gif'), ('', ':(', 'Sad', '3.gif'), ('', ':o', 'Surprised', '4.gif'), ('', ':shocked:', 'Shocked', '5.gif'), ('', ':?', 'Confused', '6.gif'), ('', ':8)', 'Cool', '7.gif'), ('', ':lol:', 'Laughing', '8.gif'), ('', ':x', 'Mad', '9.gif'), ('', ':P', 'Razz', '10.gif'), ('', ':oops:', 'Embarassed', '11.gif'), ('', ':cry:', 'Crying', '12.gif'), ('', ':evil:', 'Evil', '13.gif'), ('', ':twisted:', 'Twisted Evil', '14.gif'), ('', ':roll:', 'Rolling Eyes', '15.gif'), ('', ':wink:', 'Wink', '16.gif'), ('', ':!:', 'Exclamation', '17.gif'), ('', ':?:', 'Question', '18.gif'), ('', ':idea:', 'Idea', '19.gif'), ('', ':arrow:', 'Arrow', '20.gif'), ('', ':|', 'Neutral', '21.gif'), ('', ':mrgreen:', 'Mr. Green', '22.gif');