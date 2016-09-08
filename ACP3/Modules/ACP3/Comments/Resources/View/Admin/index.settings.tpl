{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        {include file="asset:System/Partials/form_group.select.tpl" options=$dateformat required=true label={lang t="system|date_format"}}
        {if isset($allow_emoticons)}
            <div class="form-group">
                <label for="{$allow_emoticons.0.id}" class="col-sm-2 control-label required">{lang t="comments|allow_emoticons"}</label>

                <div class="col-sm-10">
                    <div class="btn-group" data-toggle="buttons">
                        {foreach $allow_emoticons as $row}
                            <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                                <input type="radio" name="emoticons" id="{$row.id}" value="{$row.value}"{$row.checked}>
                                {$row.lang}
                            </label>
                        {/foreach}
                    </div>
                </div>
            </div>
        {/if}
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/comments"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
