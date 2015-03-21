<textarea name="{$wysiwyg.name}" id="{$wysiwyg.id}" cols="60" rows="6" class="form-control">{$wysiwyg.value}</textarea>
{javascripts}
    {$wysiwyg.js}
{/javascripts}
{if $wysiwyg.advanced === true}
    <div id="page-break-form" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">{lang t="system|insert_page_break"}</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="toc-title">{lang t="system|title_for_toc"}</label>
                        <input type="text" id="toc-title" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary">{lang t="system|submit"}</button>
                </div>
            </div>
        </div>
    </div>
    <div id="page-break-link" class="align-left">
        <a href="#" class="btn btn-default" data-toggle="modal" data-target="#page-break-form">{lang t="system|insert_page_break"}</a>
    </div>
    {javascripts}
        <script type="text/javascript">
            var wysiwygCallback = function(text) {
                {$wysiwyg.advanced_replace_content}
            };
        </script>
        {include_js module="system" file="wysiwyg" depends="bootstrap"}
    {/javascripts}
{/if}