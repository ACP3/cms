<?php
if (defined('IN_ADM') === false)
	exit;

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('system/acp_extensions.tpl'));
