<?php

namespace ACP3\Core\WYSIWYG;

/**
 * Implementation of the AbstractWYSIWYG class for TinyMCE
 * @package ACP3\Core\WYSIWYG
 */
class TinyMCE extends Textarea
{
    /**
     * @var bool
     */
    private $initialized = false;
    /**
     * @inheritdoc
     */
    public function setParameters(array $params = [])
    {
        parent::setParameters($params);

        $this->config['toolbar'] = (isset($params['toolbar'])) ? $params['toolbar'] : '';
        $this->config['height'] = ((isset($params['height'])) ? $params['height'] : 250) . 'px';
    }

    /**
     * @inheritdoc
     */
    public function display()
    {
        $editor = '';

        if ($this->initialized === false) {
            $this->initialized = true;

            $editor .= '<script type="text/javascript" src="' . ROOT_DIR . 'libraries/tinymce/tinymce.min.js"></script>';
        }

        $editor .= '<script type="text/javascript">' . "\n";
        $editor .= 'tinymce.init(' . $this->_configure() . ');' . "\n";
        $editor .= "</script>\n";

        $wysiwyg = [
            'id' => $this->id,
            'name' => $this->name,
            'value' => $this->value,
            'js' => $editor,
            'advanced' => $this->advanced,
        ];

        if ($wysiwyg['advanced'] === true) {
            $wysiwyg['advanced_replace_content'] = 'tinyMCE.execInstanceCommand(\'' . $this->id . '\',"mceInsertContent",false,text);';
        }

        /** @var \ACP3\Core\View $view */
        $view = $this->container->get('core.view');

        $view->assign('wysiwyg', $wysiwyg);
        return $view->fetchTemplate('system/wysiwyg.tpl');
    }

    /**
     * @return string
     */
    private function _configure()
    {
        $config = [
            'selector' => 'textarea#' . $this->id,
            'theme' => 'modern',
            'height' => $this->config['height'],
            'content_css' => $this->container->get('core.assets')->buildMinifyLink('css')
        ];

        // Basic editor
        if (isset($this->config['toolbar']) && $this->config['toolbar'] === 'simple') {
            $config['plugins'] = [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table contextmenu paste'
            ];
            $config['toolbar'] = 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image';

            return json_encode($config);
        } else { // Full editor
            $config['plugins'] = [
                'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker'
            ];
            $config['toolbar'] = 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons';
            $config['image_advtab'] = true;

            // Filebrowser
            $fileBrowserOptions = [
                'file' => ROOT_DIR . 'libraries/kcfinder/browse.php?opener=tinymce4&field= + field + &cms=acp3&type= + (type == "image" ? "gallery" : "files")',
                'title' => 'KCFinder',
                'width' => 700,
                'height' => 500,
                'inline' => true,
                'close_previous' => false
            ];
            $fileBrowserCallback = ",\"file_browser_callback\": function(field, url, type, win) {
                    tinyMCE.activeEditor.windowManager.open(" . json_encode($fileBrowserOptions) . ", {
                    window: win,
                    input: field
                });
                return false;
            }";

            // Ugly hack to prevent the callback function getting converted into a string
            return substr(json_encode($config), 0, -1) . $fileBrowserCallback . '}';
        }
    }
}
