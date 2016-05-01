{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if isset($error_msg)}
        {$error_msg}
    {/if}
    <form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
        <div class="form-group">
            <label for="date-format" class="col-sm-2 control-label required">{lang t="system|date_format"}</label>

            <div class="col-sm-10">
                <select class="form-control" name="dateformat" id="date-format">
                    <option value="">{lang t="system|pls_select"}</option>
                    {foreach $dateformat as $row}
                        <option value="{$row.value}"{$row.selected}>{$row.lang}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="sidebar-entries" class="col-sm-2 control-label required">{lang t="system|sidebar_entries_to_display"}</label>

            <div class="col-sm-10">
                <select class="form-control" name="sidebar" id="sidebar-entries">
                    <option>{lang t="system|pls_select"}</option>
                    {foreach $sidebar_entries as $row}
                        <option value="{$row.value}"{$row.selected}>{$row.value}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="{$readmore.0.id}" class="col-sm-2 control-label required">{lang t="news|activate_readmore"}</label>

            <div class="col-sm-10">
                <div class="btn-group" data-toggle="buttons">
                    {foreach $readmore as $row}
                        <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                            <input type="radio" name="readmore" id="{$row.id}" value="{$row.value}"{$row.checked}>
                            {$row.lang}
                        </label>
                    {/foreach}
                </div>
            </div>
        </div>
        <div id="readmore-container" class="form-group">
            <label for="readmore-chars" class="col-sm-2 control-label required">{lang t="news|readmore_chars"}</label>

            <div class="col-sm-10">
                <input class="form-control" type="number" name="readmore_chars" id="readmore-chars" value="{$readmore_chars}">
            </div>
        </div>
        <div class="form-group">
            <label for="{$category_in_breadcrumb.0.id}" class="col-sm-2 control-label required">{lang t="news|display_category_in_breadcrumb"}</label>

            <div class="col-sm-10">
                <div class="btn-group" data-toggle="buttons">
                    {foreach $category_in_breadcrumb as $row}
                        <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                            <input type="radio" name="category_in_breadcrumb" id="{$row.id}" value="{$row.value}"{$row.checked}>
                            {$row.lang}
                        </label>
                    {/foreach}
                </div>
            </div>
        </div>
        {if isset($allow_comments)}
            <div class="form-group">
                <label for="{$allow_comments.0.id}" class="col-sm-2 control-label required">{lang t="system|allow_comments"}</label>

                <div class="col-sm-10">
                    <div class="btn-group" data-toggle="buttons">
                        {foreach $allow_comments as $row}
                            <label for="{$row.id}" class="btn btn-default{if !empty($row.checked)} active{/if}">
                                <input type="radio" name="comments" id="{$row.id}" value="{$row.value}"{$row.checked}>
                                {$row.lang}
                            </label>
                        {/foreach}
                    </div>
                </div>
            </div>
        {/if}
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
                <a href="{uri args="acp/news"}" class="btn btn-default">{lang t="system|cancel"}</a>
                {$form_token}
            </div>
        </div>
    </form>
    {javascripts}
        {include_js module="news" file="admin/index.settings"}
        {include_js module="system" file="forms"}
    {/javascripts}
{/block}
