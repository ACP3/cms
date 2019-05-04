{if !$tinymce.initialized}
    <script src="{$ROOT_DIR}vendor/tinymce/tinymce/tinymce.min.js"></script>
    {if $tinymce.filemanager_path !== null}
        <script>
            const tinyMCEFileBrowserCallback = (field, url, type, win) => {
                const elem = document.documentElement,
                    body = document.getElementsByTagName('body')[0],
                    x = win.innerWidth || elem.clientWidth || body.clientWidth,
                    y = win.innerHeight|| elem.clientHeight|| body.clientHeight;

                let fileBrowserUrl = '{$tinymce.filemanager_path}?field_name=' + field;
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
        </script>
    {/if}
{/if}
{include_js module="Wysiwygtinymce" file="partials/tinymce"}
