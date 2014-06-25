<?php

namespace ACP3\Core\WYSIWYG;

/**
 * Implementation of the AbstractWYSIWYG class for TinyMCE
 * @package ACP3\Core\WYSIWYG
 */
class TinyMCE extends AbstractWYSIWYG
{

    public function __construct($id, $name, $value = '', $toolbar = '', $advanced = false, $height = '')
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
        $this->advanced = (bool)$advanced;
        $this->config['toolbar'] = $toolbar;
        $this->config['height'] = $height . 'px';
    }

    protected function configure()
    {
        return;
    }

    /**
     * @return string
     */
    public function display()
    {
        $editor = '<script type="text/javascript" src="' . ROOT_DIR . 'libraries/tinymce/tinymce.min.js"></script>';
        $editor .= '<script type="text/javascript">' . "\n";
        $editor .= "tinymce.init({\n";
        $editor .= 'selector : "textarea#' . $this->id . '",' . "\n";
        $editor .= 'theme : "modern",' . "\n";
        $editor .= 'height : "' . $this->config['height'] . '",' . "\n";

        if (isset($this->config['toolbar']) && $this->config['toolbar'] === 'simple') {
            $editor .= 'plugins: ["advlist autolink lists link image charmap print preview anchor","searchreplace visualblocks code fullscreen","insertdatetime media table contextmenu paste"],' . "\n";
            $editor .= 'toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"' . "\n";
        } else {
            $editor.= "file_browser_callback: function(field, url, type, win) {
                    tinyMCE.activeEditor.windowManager.open({
                    file: '" . ROOT_DIR . "libraries/kcfinder/browse.php?opener=tinymce4&field=' + field + '&cms=acp3&type=' + (type == 'image' ? 'gallery' : 'files'),
                    title: 'KCFinder',
                    width: 700,
                    height: 500,
                    inline: true,
                    close_previous: false
                }, {
                    window: win,
                    input: field
                });
                return false;
            },";
            $editor .= 'plugins: [
        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime media nonbreaking save table contextmenu directionality",
        "emoticons template paste textcolor colorpicker"],' . "\n";
            $editor .= 'image_advtab: true,' . "\n";
            $editor .= 'toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons"' . "\n";
        }

        // Filebrowser
        $editor .= "});\n";
        $editor .= "</script>\n";
        $editor .= '<textarea name="' . $this->name . '" id="' . $this->id . '" cols="50" rows="5" style="width:100%">' . $this->value . "</textarea>\n";

        $wysiwyg = array(
            'id' => $this->id,
            'editor' => $editor,
            'advanced' => $this->advanced,
        );

        if ($wysiwyg['advanced'] === true)
            $wysiwyg['advanced_replace_content'] = 'tinyMCE.execInstanceCommand(\'' . $this->id . '\',"mceInsertContent",false,text);';

        \ACP3\Core\Registry::get('View')->assign('wysiwyg', $wysiwyg);
        return \ACP3\Core\Registry::get('View')->fetchTemplate('system/wysiwyg.tpl');
    }

}