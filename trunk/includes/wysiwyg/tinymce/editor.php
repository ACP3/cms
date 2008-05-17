<?php
function editor($params) {
	static $init = false;

	$id = substr($params['name'], 5, -1);
	$out = '';

	if (!$init) {
		$out.= '<script language="javascript" type="text/javascript" src="' . ROOT_DIR . 'includes/wysiwyg/tinymce/tiny_mce.js"></script>' . "\n";
	}

	$out.= '<script language="javascript" type="text/javascript">' . "\n";
	$out.= "tinyMCE.init({\n";
	$out.= 'mode : "exact",' . "\n";
	$out.= 'elements : "' . $id . '",' . "\n";
	$out.= 'theme : "advanced",' . "\n";
	$out.= 'theme_advanced_toolbar_location : "top",' . "\n";
	$out.= 'theme_advanced_toolbar_align : "left",' . "\n";

	if (isset($params['toolbar']) && $params['toolbar'] == 'simple') {
		$out.= 'plugins : "inlinepopups",' . "\n";
		$out.= 'theme_advanced_buttons1 : "bold,italic,|,numlist,bullist,|,link,unlink,anchor,|,undo,redo,|,help",' . "\n";
		$out.= 'theme_advanced_buttons2 : "",' . "\n";
		$out.= 'theme_advanced_buttons2 : "",' . "\n";
	} else {
		$out.= 'plugins : "safari,style,layer,table,advhr,advimage,advlink,emotions,inlinepopups,preview,media,searchreplace,contextmenu,paste,noneditable,visualchars,nonbreaking,xhtmlxtras",' . "\n";
		$out.= 'theme_advanced_buttons1 : "code,|,newdocument,preview,|,cut,copy,paste,pastetext,pasteword,|,undo,redo,|,search,replace,|,cleanup,removeformat",' . "\n";
		$out.= 'theme_advanced_buttons2 : "bold,italic,underline,strikethrough,|,sub,sup,|,numlist,bullist,|,outdent,indent,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink,anchor,|,image,media,advhr,emotions,charmap",' . "\n";
		$out.= 'theme_advanced_buttons3 : "styleselect,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor,|,visualaid,|,tablecontrols",' . "\n";
		$out.= 'theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,|,help",' . "\n";
	}
	$out.= 'width : "100%",' . "\n";
	$out.= 'height : "' . $params['height'] . '",' . "\n";
	$out.= "});\n";
	$out.= "</script>\n";
	$out.= '<textarea name="' . $params['name'] . '" id="' . $id . '" cols="50" rows="5">' . (!empty($params['value']) ? $params['value'] : '') . "</textarea>\n";

	$init = true;

	return $out;
}
?>