{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="date-format" class="col-sm-2 control-label required">{lang t="system|date_format"}</label>

            <div class="col-sm-10">
                <select class="form-control" name="dateformat" id="date-format" required>
                    {foreach $dateformat as $row}
                        <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="sidebar-entries" class="col-sm-2 control-label required">{lang t="system|sidebar_entries_to_display"}</label>

            <div class="col-sm-10">
                <select class="form-control" name="sidebar" id="sidebar-entries" required>
                    {foreach $sidebar_entries as $row}
                        <option value="{$row.value}"{$row.selected}>{$row.value}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        {if isset($comments)}
            <div class="form-group">
                <label for="{$comments.0.id}" class="col-sm-2 control-label required">{lang t="system|allow_comments"}</label>

                <div class="col-sm-10">
                    <div class="btn-group" data-toggle="buttons">
                        {foreach $comments as $row}
                            <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                                <input type="radio" name="comments" id="{$row.id}" value="{$row.value}"{$row.checked}>
                                {$row.lang}
                            </label>
                        {/foreach}
                    </div>
                </div>
            </div>
        {/if}
        {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token back_url={uri args="acp/files"}}
    </form>
    {javascripts}
        {include_js module="system" file="ajax-form"}
    {/javascripts}
{/block}
