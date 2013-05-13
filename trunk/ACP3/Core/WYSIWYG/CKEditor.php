<?php

namespace ACP3\Core\WYSIWYG;

class CKEditor extends AbstractWYSIWYG {
	public function __construct($id, $name, $value = '', $toolbar = '', $advanced = false, $height = '') {
		$this->id = $id;
		$this->name = $name;
		$this->value = $value;
		$this->advanced = (bool) $advanced;
		$this->config['toolbar'] = $toolbar === 'simple' ? 'Basic' : 'Full';
		$this->config['height'] = $height . 'px';

		$this->configure();
	}	

	protected function configure() {
		$filebrowser_uri = ROOT_DIR . 'libraries/kcfinder/browse.php%s&cms=acp3';
		$upload_uri = ROOT_DIR . 'libraries/kcfinder/upload.php%s&cms=acp3';

		$this->config = array();
		$this->config['filebrowserBrowseUrl'] = sprintf($filebrowser_uri, '?type=files&amp;cms=');
		$this->config['filebrowserImageBrowseUrl'] = sprintf($filebrowser_uri, '?type=gallery');
		$this->config['filebrowserFlashBrowseUrl'] = sprintf($filebrowser_uri, '?type=files');
		$this->config['filebrowserUploadUrl'] = sprintf($upload_uri, '?type=files');
		$this->config['filebrowserImageUploadUrl'] = sprintf($upload_uri, '?type=gallery');
		$this->config['filebrowserFlashUploadUrl'] = sprintf($upload_uri, '?type=files');

		$this->config['extraPlugins'] = 'divarea,oembed,codemirror';
		$this->config['allowedContent'] = true;
		$this->config['codemirror'] = '@@{ theme: \'default\',
			lineNumbers: true,
			lineWrapping: true,
			matchBrackets: true,
			autoCloseTags: true,
			autoCloseBrackets: true,
			enableSearchTools: true,
			enableCodeFolding: true,
			enableCodeFormatting: true,
			autoFormatOnStart: true,
			autoFormatOnUncomment: true,
			highlightActiveLine: true,
			highlightMatches: true,
			showFormatButton: false,
			showCommentButton: false,
			showUncommentButton: false
		}';

		// Smilies
		if ((!isset($this->config['toolbar']) || $this->config['toolbar'] !== 'simple') && \ACP3\Core\Modules::isActive('emoticons') === true) {
			$this->config['smiley_path'] = ROOT_DIR . 'uploads/emoticons/';
			$this->config['smiley_images'] = $this->config['smiley_descriptions'] = '';
			$emoticons = \ACP3\CMS::$injector['Db']->fetchAll('SELECT description, img FROM ' . DB_PRE . 'emoticons');
			$c_emoticons = count($emoticons);

			for ($i = 0; $i < $c_emoticons; ++$i) {
				$this->config['smiley_images'].= '\'' . $emoticons[$i]['img'] . '\',';
				$this->config['smiley_descriptions'].= '\'' . $emoticons[$i]['description'] . '\',';
			}

			$this->config['smiley_images'] = '@@[' . substr($this->config['smiley_images'], 0, -1) . ']';
			$this->config['smiley_descriptions'] = '@@[' . substr($this->config['smiley_descriptions'], 0, -1) . ']';
		}
		// Basic Toolbar erweitern
		if (isset($this->config['toolbar']) && $this->config['toolbar'] == 'Basic') {
			$this->config['toolbar_Basic'] = "@@[ ['Source','-','Undo','Redo','-','Bold','Italic','-','NumberedList','BulletedList','-','Link','Unlink','-','About'] ]";
		}
	}

	public function display() {
		require_once ACP3_ROOT_DIR . 'libraries/ckeditor/ckeditor.php';

		$ckeditor = new \CKEditor(ROOT_DIR . 'libraries/ckeditor/');
		$ckeditor->returnOutput = true;

		$wysiwyg = array(
			'id' => $this->id,
			'editor' => $ckeditor->editor($this->name, $this->id, $this->value, $this->config),
			'advanced' => $this->advanced,
		);

		if ($wysiwyg['advanced'] === true)
			$wysiwyg['advanced_replace_content'] = 'CKEDITOR.instances.' . $wysiwyg['id'] . '.insertHtml(text);';

		\ACP3\CMS::$injector['View']->assign('wysiwyg', $wysiwyg);
		return \ACP3\CMS::$injector['View']->fetchTemplate('system/wysiwyg.tpl');
	}

}