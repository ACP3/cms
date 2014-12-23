<?php

namespace ACP3\Core\WYSIWYG;

use ACP3\Core;

/**
 * Implementation of the AbstractWYSIWYG class for CKEditor
 * @package ACP3\Core\WYSIWYG
 */
class CKEditor extends Textarea
{
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var \ACP3\Modules\Emoticons\Model
     */
    private $emoticonsModel;
    /**
     * @var \ACP3\Core\WYSIWYG\CKEditor\Initialize
     */
    private $editor;

    public function __construct(
        Core\Modules $modules,
        Core\View $view)
    {
        $this->modules = $modules;
        $this->view = $view;
    }

    /**
     * @param \ACP3\Modules\Emoticons\Model $emoticonsModel
     *
     * @return $this
     */
    public function setEmoticonsModel(\ACP3\Modules\Emoticons\Model $emoticonsModel)
    {
        $this->emoticonsModel = $emoticonsModel;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setParameters(array $params = [])
    {
        parent::setParameters($params);

        $this->config['toolbar'] = (isset($params['toolbar']) && $params['toolbar'] === 'simple') ? 'Basic' : 'Full';
        $this->config['height'] = ((isset($params['height']) ? $params['height'] : 250)) . 'px';
    }

    /**
     * @inheritdoc
     */
    public function display()
    {
        $this->_configure();

        // CKEditor is currently not initialized
        if (!$this->editor) {
            $this->editor = new CKEditor\Initialize(ROOT_DIR . 'libraries/ckeditor/');
        }

        $wysiwyg = [
            'id' => $this->id,
            'name' => $this->name,
            'value' => $this->value,
            'js' => $this->editor->editor($this->name, $this->config),
            'advanced' => $this->advanced,
        ];

        if ($wysiwyg['advanced'] === true) {
            $wysiwyg['advanced_replace_content'] = 'CKEDITOR.instances.' . $wysiwyg['id'] . '.insertHtml(text);';
        }

        $this->view->assign('wysiwyg', $wysiwyg);
        return $this->view->fetchTemplate('system/wysiwyg.tpl');
    }

    /**
     * @inheritdoc
     */
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
            if ($this->modules->isActive('emoticons') === true) {
                $this->config['smiley_path'] = ROOT_DIR . 'uploads/emoticons/';
                $this->config['smiley_images'] = $this->config['smiley_descriptions'] = '';
                $emoticons = $this->emoticonsModel->getAll();
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
