<?php
function smarty_function_seo_fields($params, $template)
{
	$seo = array(
		'enable_uri_aliases' => CONFIG_SEO_ALIASES,
		'alias' => isset($params['alias']) ? $params['alias'] : '',
		'keywords' => $params['keywords'],
		'description' => $params['description']
	);

	$template->smarty->assign('seo', $seo);
	return $template->smarty->fetch('common/seo_fields.tpl');;
}
/* vim: set expandtab: */
?>