<?php
/**
 * Polls
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;
if (!$modules->check('polls', 'entry'))
	redirect('errors/403');

switch ($modules->action) {
	case 'create':
		break;
	case 'edit':
		break;
	case 'delete':
		break;
	default:
		redirect('errors/404');
}
?>