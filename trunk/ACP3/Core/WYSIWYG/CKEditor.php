<?php

namespace ACP3\Core\WYSIWYG;

/**
 * Implementation of the AbstractWYSIWYG class for CKEditor
 * @package ACP3\Core\WYSIWYG
 */
class CKEditor extends AbstractWYSIWYG
{
    /**
     * @param array $params
     */
    public function setParameters(array $params = [])
    {
        $this->id = $params['id'];
        $this->name = $params['name'];
        $this->value = $params['value'];
        $this->advanced = isset($params['advanced']) ? (bool)$params['advanced'] : false;

        $this->config['toolbar'] = isset($params['toolbar']) && $params['toolbar'] === 'simple' ? 'Basic' : 'Full';
        $this->config['height'] = (isset($params['height']) ? $params['height'] : 250) . 'px';
    }

    /**
     * @return string
     */
    public function display()
    {
        $this->_configure();

        require_once LIBRARIES_DIR . 'ckeditor/ckeditor.php';

        $ckeditor = new \CKEditor(ROOT_DIR . 'libraries/ckeditor/');
        $ckeditor->returnOutput = true;

        $wysiwyg = [
            'id' => $this->id,
            'editor' => $ckeditor->editor($this->name, $this->id, $this->value, $this->config),
            'advanced' => $this->advanced,
        ];

        if ($wysiwyg['advanced'] === true) {
            $wysiwyg['advanced_replace_content'] = 'CKEDITOR.instances.' . $wysiwyg['id'] . '.insertHtml(text);';
        }

        $view = $this->container->get('core.view');

        $view->assign('wysiwyg', $wysiwyg);
        return $view->fetchTemplate('system/wysiwyg.tpl');
    }

    private function _configure()
    {
        $filebrowserUri = ROOT_DIR . 'libraries/kcfinder/browse.php?opener=ckeditor%s&cms=acp3';
        $uploadUri = ROOT_DIR . 'libraries/kcfinder/upload.php?opener=ckeditor%s&cms=acp3';

        $this->config = [];
        $this->config['filebrowserBrowseUrl'] = sprintf($filebrowserUri, '&type=files');
        $this->config['filebrowserImageBrowseUrl'] = sprintf($filebrowserUri, '&type=gallery');
        $this->config['filebrowserFlashBrowseUrl'] = sprintf($filebrowserUri, '&type=files');
        $this->config['filebrowserUploadUrl'] = sprintf($uploadUri, '&type=files');
        $this->config['filebrowserImageUploadUrl'] = sprintf($uploadUri, '&type=gallery');
        $this->config['filebrowserFlashUploadUrl'] = sprintf($uploadUri, '&type=files');

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
        if ((!isset($this->config['toolbar']) || $this->config['toolbar'] !== 'simple') && $this->container->get('core.modules')->isActive('emoticons') === true) {
            $this->config['smiley_path'] = ROOT_DIR . 'uploads/emoticons/';
            $this->config['smiley_images'] = $this->config['smiley_descriptions'] = '';
            $emoticons = $this->container->get('emoticons.model')->getAll();
            $c_emoticons = count($emoticons);

            for ($i = 0; $i < $c_emoticons; ++$i) {
                $this->config['smiley_images'] .= '\'' . $emoticons[$i]['img'] . '\',';
                $this->config['smiley_descriptions'] .= '\'' . $emoticons[$i]['description'] . '\',';
            }

            $this->config['smiley_images'] = '@@[' . substr($this->config['smiley_images'], 0, -1) . ']';
            $this->config['smiley_descriptions'] = '@@[' . substr($this->config['smiley_descriptions'], 0, -1) . ']';
        }
        // Basic Toolbar erweitern
        if (isset($this->config['toolbar']) && $this->config['toolbar'] == 'Basic') {
            $this->config['toolbar_Basic'] = "@@[ ['Source','-','Undo','Redo','-','Bold','Italic','-','NumberedList','BulletedList','-','Link','Unlink','-','About'] ]";
        }
    }

}