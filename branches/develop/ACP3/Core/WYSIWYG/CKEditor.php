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

        $ckeditor = new CKEditor\Initialize(ROOT_DIR . 'libraries/ckeditor/');
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
        $this->config['extraPlugins'] = 'divarea,oembed,codemirror';
        $this->config['allowedContent'] = true;
        $this->config['codemirror'] = [
            'theme' => 'default',
            'lineNumbers' => true,
            'lineWrapping' => true,
            'matchBrackets' => true,
            'autoCloseTags' => true,
            'autoCloseBrackets' => true,
            'enableSearchTools' => true,
            'enableCodeFolding' => true,
            'enableCodeFormatting' => true,
            'autoFormatOnStart' => true,
            'autoFormatOnUncomment' => true,
            'highlightActiveLine' => true,
            'highlightMatches' => true,
            'showFormatButton' => false,
            'showCommentButton' => false,
            'showUncommentButton' => false
        ];

        // Full toolbar
        if ((!isset($this->config['toolbar']) || $this->config['toolbar'] !== 'Basic')) {
            $fileBrowserUri = ROOT_DIR . 'libraries/kcfinder/browse.php?opener=ckeditor%s&cms=acp3';
            $uploadUri = ROOT_DIR . 'libraries/kcfinder/upload.php?opener=ckeditor%s&cms=acp3';

            $this->config['filebrowserBrowseUrl'] = sprintf($fileBrowserUri, '&type=files');
            $this->config['filebrowserImageBrowseUrl'] = sprintf($fileBrowserUri, '&type=gallery');
            $this->config['filebrowserFlashBrowseUrl'] = sprintf($fileBrowserUri, '&type=files');
            $this->config['filebrowserUploadUrl'] = sprintf($uploadUri, '&type=files');
            $this->config['filebrowserImageUploadUrl'] = sprintf($uploadUri, '&type=gallery');
            $this->config['filebrowserFlashUploadUrl'] = sprintf($uploadUri, '&type=files');

            // Include emoticons, if available
            if ($this->container->get('core.modules')->isActive('emoticons') === true) {
                $this->config['smiley_path'] = ROOT_DIR . 'uploads/emoticons/';
                $this->config['smiley_images'] = $this->config['smiley_descriptions'] = '';
                $emoticons = $this->container->get('emoticons.model')->getAll();
                $c_emoticons = count($emoticons);

                $images = $descriptions = [];
                for ($i = 0; $i < $c_emoticons; ++$i) {
                    $images[] = $emoticons[$i]['img'];
                    $descriptions[] = $emoticons[$i]['description'];
                }

                $this->config['smiley_images'] = [$images];
                $this->config['smiley_descriptions'] = [$descriptions];
            }
        } else { // basic toolbar
            $this->config['extraPlugins'] = 'divarea,codemirror';
            $this->config['toolbar'] = [
                [
                    'Source', '-', 'Undo', 'Redo', '-', 'Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', '-', 'About'
                ]
            ];
        }
    }
}
