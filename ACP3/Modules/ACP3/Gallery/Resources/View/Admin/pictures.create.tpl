{extends file="asset:layout.tpl"}

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
            <div class="form-group">
                <label for="{$options.0.name}" class="col-sm-2 control-label">{lang t="system|options"}</label>
                <div class="col-sm-10">
                    {foreach $options as $row}
                        <div class="checkbox">
                            <label for="{$row.name}">
                                <input type="checkbox" name="{$row.name}" id="{$row.name}" value="1"{$row.checked}>
                                {$row.lang}
                            </label>
                        </div>
                    {/foreach}
                </div>
            </div>
        {/if}
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                <a href="{uri args="acp/gallery/edit/id_`$gallery_id`"}" class="btn btn-default">{lang t="system|cancel"}</a>
                {$form_token}
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="system" file="forms"}
    {/javascripts}
{/block}