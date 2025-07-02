{if !$tinymce.initialized}
    {if $tinymce.filemanager_path !== null}
        <script>
            let windowHandle;
            let tinymceFilePickerCallback;

            const tinyMCEFileBrowserCallback = (callback) => {
                const elem = document.documentElement,
                    body = document.querySelector('body'),
                    x = window.innerWidth || elem.clientWidth || body.clientWidth,
                    y = window.innerHeight|| elem.clientHeight|| body.clientHeight;

                let fileBrowserUrl = '{$tinymce.filemanager_path}';

                const width = screen.width * 0.7;
                const height = screen.height * 0.7;
                const left = (screen.width - width) / 2;
                const top = (screen.height - height) / 2;
                let windowOptions = "toolbar=no,status=no,resizable=yes,dependent=yes";
                windowOptions += ",width=" + width;
                windowOptions += ",height=" + height;
                windowOptions += ",left=" + left;
                windowOptions += ",top=" + top;

                tinymceFilePickerCallback = callback;
                windowHandle = window.open(fileBrowserUrl, "richfilemanager-popup", windowOptions);
            }

            window.SetUrl = (url) => {
                if (windowHandle) {
                    windowHandle.close();
                }

                tinymceFilePickerCallback(url);
            };
        </script>
    {/if}
{/if}
{include_js module="Wysiwygtinymce" file="tinymce/tinymce"}
{include_js module="Wysiwygtinymce" file="partials/tinymce"}
