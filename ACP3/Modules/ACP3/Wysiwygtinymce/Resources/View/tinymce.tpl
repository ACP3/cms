{if !$config.is_initialized}
    <script src="{$ROOT_DIR}vendor/tinymce/tinymce/tinymce.min.js"></script>
{/if}
<script>
    tinymce.init({
        selector: '{$config.selector}',
        theme: '{$config.theme}',
        height: '{$config.height}',
        content_css: '{$config.content_css}',
        plugins: {$config.plugins},
        toolbar: '{$config.toolbar}',
        image_advtab: {$config.image_advtab}
        {if isset($config.filemanager_path)}
            ,
            file_browser_callback: function (field, url, type, win) {
                const elem = document.documentElement,
                    body = document.getElementsByTagName('body')[0],
                    x = win.innerWidth || elem.clientWidth || body.clientWidth,
                    y = win.innerHeight|| elem.clientHeight|| body.clientHeight;

                let fileBrowserUrl = '{$config.filemanager_path}?field_name=' + field;
                if (type === 'image') {
                    fileBrowserUrl += '&filter=image';
                }

                tinyMCE.activeEditor.windowManager.open({
                    file: fileBrowserUrl,
                    title: '{lang t="filemanager|filemanager"}',
                    width: x * 0.8,
                    height: y * 0.8,
                    close_previous: false
                }, {
                    window: win,
                    input: field
                });
            }
        {/if}
    });

    jQuery(document).ready(function ($) {
        $(document).on('acp3.ajaxFrom.submit.before', function () {
            if (typeof tinymce !== "undefined") {
                tinymce.triggerSave();
            }
        });
    });
</script>
