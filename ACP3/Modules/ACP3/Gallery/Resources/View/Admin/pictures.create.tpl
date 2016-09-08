{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        {block GALLERY_PICTURE_UPLOAD}
            <div class="form-group">
                <label for="file" class="col-sm-2 control-label required">{lang t="gallery|select_picture"}</label>
                <div class="col-sm-10"><input type="file" name="file" id="file" required></div>
            </div>
        {/block}
        <div class="form-group">
            <label for="description" class="col-sm-2 control-label">{lang t="system|description"}</label>
            <div class="col-sm-10">{wysiwyg name="description" value="`$form.description`" height="150" toolbar="simple"}</div>
        </div>
        {if isset($options)}
            {include file="asset:System/Partials/form_group.checkbox.tpl" options=$options label={lang t="system|options"}}
        {/if}
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/gallery/index/edit/id_`$gallery_id`"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
