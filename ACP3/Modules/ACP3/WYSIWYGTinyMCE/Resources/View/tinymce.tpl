<script type="text/javascript">
    tinymce.init({
        selector: '{$tinymce_config.selector}',
        theme: '{$tinymce_config.theme}',
        height: '{$tinymce_config.height}',
        content_css: '{$tinymce_config.content_css}',
        plugins: {$plugins},
        toolbar: '{$toolbar}',
        image_advtab: {$image_advtab},
        {if isset($filemanager_path)}
            file_browser_callback: function(field, url, type, win) {
                tinyMCE.activeEditor.windowManager.open({
                    file: '{$filemanager_path}browse.php?opener=tinymce4&field=' + field + '&cms=acp3&type=' + (type == "image" ? "gallery" : "files"),
                    title: '{lang t="filemanager|filemanager"}',
                    width: 700,
                    height: 500,
                    inline: true,
                    close_previous: false
                }, {
                    window: win,
                    input: field
                });
                return false;
            }
        {/if}
    });
</script>