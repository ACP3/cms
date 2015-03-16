{extends file="asset:layout.tpl"}

{block CONTENT}
    {if isset($sql_queries)}
        <pre>
        {foreach $sql_queries as $row}
            <span style="color:#{$row.color}">{$row.query}</span>
        {/foreach}
        </pre>
    {else}
        {if isset($error_msg)}
            {$error_msg}
        {/if}
        <form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
            <div class="form-group">
                <label for="upload-text" class="col-sm-2 control-label">{lang t="system|text"}</label>

                <div class="col-sm-10">
                    <textarea class="form-control" name="text" id="upload-text" cols="50" rows="6">{$form.text}</textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="upload-file" class="col-sm-2 control-label">{lang t="system|file"}</label>

                <div class="col-sm-10"><input type="file" name="file" id="upload-file"></div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                    {$form_token}
                </div>
            </div>
        </form>
        {javascripts}
        {if !isset($sql_queries)}
            {include_js module="system" file="forms"}
        {/if}
        {/javascripts}
    {/if}
{/block}