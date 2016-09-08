{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        {include file="asset:System/Partials/form_group.select.tpl" options=$dateformat required=true label={lang t="system|date_format"}}
        {include file="asset:System/Partials/form_group.select.tpl" options=$sidebar_entries required=true label={lang t="system|sidebar_entries_to_display"}}
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$readmore required=true label={lang t="news|activate_readmore"}}
        <div id="readmore-container" class="form-group">
            <label for="readmore-chars" class="col-sm-2 control-label required">{lang t="news|readmore_chars"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="number" name="readmore_chars" id="readmore-chars" value="{$readmore_chars}" required>
            </div>
        </div>
        {include file="asset:System/Partials/form_group.button_group.tpl" options=$category_in_breadcrumb required=true label={lang t="news|display_category_in_breadcrumb"}}
        {if isset($allow_comments)}
            {include file="asset:System/Partials/form_group.button_group.tpl" options=$allow_comments required=true label={lang t="system|allow_comments"}}
        {/if}
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/news"}}
    </form>
    {javascripts}
        {include_js module="news" file="admin/index.settings"}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
