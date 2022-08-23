{event name="core.wysiwyg.`$wysiwyg.friendly_name|lower`.before" id=$wysiwyg.id}
<textarea name="{$wysiwyg.name}"
          id="{$wysiwyg.id}"
          cols="60"
          rows="6"
          {if !empty($wysiwyg.data_config)}data-wysiwyg-config='{$wysiwyg.data_config}'{/if}
          class="wysiwyg-{$wysiwyg.friendly_name|lower} form-control"
          {if $wysiwyg.required}required{/if}>{$wysiwyg.value|escape:'html'}</textarea>
{javascripts}
    {include file="asset:System/Partials/wysiwyg_config.tpl" js=$wysiwyg.js}
{/javascripts}
{if $wysiwyg.advanced === true}
    <div id="page-break-form" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{lang t="system|insert_page_break"}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{lang t="system|close"}"></button>
                </div>
                <div class="modal-body">
                    <label for="toc-title">{lang t="system|title_for_toc"}</label>
                    <input type="text" id="toc-title" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{lang t="system|close"}</button>
                    <button class="btn btn-primary">{lang t="system|submit"}</button>
                </div>
            </div>
        </div>
    </div>
    <div id="page-break-link" class="mt-2">
        <a href="#" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#page-break-form">
            {lang t="system|insert_page_break"}
        </a>
    </div>
    {javascripts}
        <script>
            var wysiwygCallback = function(text) {
                {$wysiwyg.advanced_replace_content}
            };
        </script>
        {include_js module="system" file="partials/wysiwyg" depends=["bootstrap"]}
    {/javascripts}
{/if}
