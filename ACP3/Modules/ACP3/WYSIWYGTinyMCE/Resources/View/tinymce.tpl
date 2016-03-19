{if !$config.is_initialized}
    <script type="text/javascript" src="{$ROOT_DIR}vendor/tinymce/tinymce/tinymce.min.js"></script>
{/if}
<script type="text/javascript">
    tinymce.init({
        selector: '{$config.selector}',
        theme: '{$config.theme}',
        height: '{$config.height}',
        content_css: '{$config.content_css}',
        plugins: {$config.plugins},
        toolbar: '{$config.toolbar}',
        image_advtab: {$config.image_advtab},
        {if isset($config.filemanager_path)}
            file_browser_callback: function(field, url, type, win) {
                tinyMCE.activeEditor.windowManager.open({
                    file: '{$config.filemanager_path}browse.php?opener=tinymce4&field=' + field + '&cms=acp3&type=' + (type == "image" ? "gallery" : "files"),
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
