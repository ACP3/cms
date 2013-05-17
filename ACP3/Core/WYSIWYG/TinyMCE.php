<?php

namespace ACP3\Core\WYSIWYG;

class TinyMCE extends AbstractWYSIWYG {

	public function __construct($id, $name, $value = '', $toolbar = '', $advanced = false, $height = '') {
		$this->id = $id;
		$this->name = $name;
		$this->value = $value;
		$this->advanced = (bool) $advanced;
		$this->config['toolbar'] = $toolbar;
		$this->config['height'] = $height . 'px';
	}

	protected function configure() {
		return;
	}

	public function display() {
		// Load the TinyMCE compressor class
		require_once ACP3_ROOT_DIR . 'libraries/tinymce/tiny_mce_gzip.php';

		$tinymce_options = array(
			'url' => ROOT_DIR . 'libraries/tinymce/tiny_mce_gzip.php',
			'themes' => 'advanced',
			'languages' => 'en',
			'cache_dir' => UPLOADS_DIR . 'cache/minify/',
		);

		if ($this->config['toolbar'] === 'simple') {
			$tinymce_options['plugins'] = 'inlinepopups,contextmenu';
		} else {
			$tinymce_options['plugins'] = 'autolink,lists,safari,style,layer,table,advhr,advimage,advlink,advlist,emotions,inlinepopups,preview,media,searchreplace,contextmenu,paste,noneditable,visualchars,nonbreaking,xhtmlxtras';
		}

		// Renders script tag with compressed scripts
		$editor = \TinyMCE_Compressor::renderTag($tinymce_options, true);

		// Normale Initialisierung
		$editor.= '<script type="text/javascript">' . "\n";
		$editor.= "tinyMCE.init({\n";
		$editor.= 'mode : "exact",' . "\n";
		$editor.= 'elements : "' . $this->id . '",' . "\n";
		$editor.= 'theme : "advanced",' . "\n";
		$editor.= 'theme_advanced_toolbar_location : "top",' . "\n";
		$editor.= 'theme_advanced_toolbar_align : "left",' . "\n";
		$editor.= 'convert_urls : false,' . "\n";
		$editor.= 'entity_encoding : "numeric",' . "\n";
		$editor.= 'constrain_menus : true,' . "\n";

		if (isset($this->config['toolbar']) && $this->config['toolbar'] === 'simple') {
			$editor.= 'plugins : "inlinepopups,contextmenu",' . "\n";
			$editor.= 'theme_advanced_buttons1 : "code,|,bold,italic,|,numlist,bullist,|,link,unlink,anchor,|,undo,redo,|,help",' . "\n";
			$editor.= 'theme_advanced_buttons2 : "",' . "\n";
		} else {
			$editor.= 'plugins : "autolink,lists,safari,style,layer,table,advhr,advimage,advlink,advlist,emotions,inlinepopups,preview,media,searchreplace,contextmenu,paste,noneditable,visualchars,nonbreaking,xhtmlxtras",' . "\n";
			$editor.= 'theme_advanced_buttons1 : "code,|,newdocument,preview,|,cut,copy,paste,pastetext,pasteword,|,undo,redo,|,search,replace,|,cleanup,removeformat",' . "\n";
			$editor.= 'theme_advanced_buttons2 : "bold,italic,underline,strikethrough,|,sub,sup,|,numlist,bullist,|,outdent,indent,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink,anchor,|,image,media,advhr,emotions,charmap",' . "\n";
			$editor.= 'theme_advanced_buttons3 : "styleselect,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor,|,visualaid,|,tablecontrols",' . "\n";
			$editor.= 'theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,|,help",' . "\n";
		}
		$editor.= 'height : "' . $this->config['height'] . '",' . "\n";

		// Filebrowser
		$editor.= 'file_browser_callback: "openKCFinder",' . "\n";
		$editor.= "});\n";
		$editor.= "function openKCFinder(field_name, url, type, win) {
		tinyMCE.activeEditor.windowManager.open({
			file: '" . ROOT_DIR . "libraries/kcfinder/browse.php?opener=tinymce&cms=acp3&type=' + (type == 'image' ? 'gallery' : 'files'),
			title: 'KCFinder',
			width: 700,
			height: 500,
			resizable: 'yes',
			inline: true,
			close_previous: 'no',
			popup_css: false
		}, {
			window: win,
			input: field_name
		});
		return false;}\n";
		$editor.= "</script>\n";
		$editor.= '<textarea name="' . $this->name . '" id="' . $this->id . '" cols="50" rows="5" style="width:100%">' . $this->value . "</textarea>\n";

		$wysiwyg = array(
			'id' => $this->id,
			'editor' => $editor,
			'advanced' => $this->advanced,
		);

		if ($wysiwyg['advanced'] === true)
			$wysiwyg['advanced_replace_content'] = 'tinyMCE.execInstanceCommand(\'' . $this->id . '\',"mceInsertContent",false,text);';

		Registry::getClass('View')->assign('wysiwyg', $wysiwyg);
		return Registry::getClass('View')->fetchTemplate('system/wysiwyg.tpl');
	}

}