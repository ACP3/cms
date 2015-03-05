{extends file="asset:layout.tpl"}

{block CONTENT}
    <form action="{uri args="acp/newsletter/index/delete"}" method="post">
        <nav id="adm-list" class="navbar navbar-default" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex2-collapse">
                    <span class="sr-only">{lang t="system|toggle_navigation"}</span>
                    <span class="icon-bar"></span> <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <span class="navbar-brand">{lang t="system|overview"}</span>
            </div>
            <div class="collapse navbar-collapse navbar-ex2-collapse">
                <div class="navbar-text pull-right">
                    {check_access mode="link" path="acp/newsletter/index/create" class="glyphicon glyphicon-plus text-success"}
                    {check_access mode="link" path="acp/newsletter/accounts" class="glyphicon glyphicon-user text-info"}
                    {check_access mode="link" path="acp/newsletter/index/settings" class="glyphicon glyphicon-cog"}
                    {if isset($newsletter)}
                        {check_access mode="button" path="acp/newsletter/index/delete" class="glyphicon glyphicon-remove text-danger" lang="system|delete_marked"}
                    {/if}
                </div>
            </div>
        </nav>
        {redirect_message}
        {if isset($newsletter)}
            {assign "statusSearch" array('0', '1')}
            {assign "statusReplace" array({lang t="newsletter|not_yet_sent"}, {lang t="newsletter|already_sent"})}
            <table id="acp-table" class="table table-striped table-hover">
                <thead>
                <tr>
                    {if $can_delete === true}
                        <th style="width:3%"><input type="checkbox" id="mark-all" value="1" {mark name="entries"}></th>
                    {/if}
                    <th style="width:22%">{lang t="system|date"}</th>
                    <th>{lang t="newsletter|subject"}</th>
                    <th>{lang t="newsletter|status"}</th>
                    {if $can_send && $has_active_newsletter_accounts}
                        <th>{lang t="system|options"}</th>
                    {/if}
                    <th style="width:5%">{lang t="system|id"}</th>
                </tr>
                </thead>
                <tbody>
                {foreach $newsletter as $row}
                    <tr>
                        {if $can_delete === true}
                            <td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
                        {/if}
                        <td>{date_range start=$row.date}</td>
                        <td>{check_access mode="link" path="acp/newsletter/index/edit/id_`$row.id`" title=$row.title}</td>
                        <td>{$row.status|replace:$statusSearch:$statusReplace}</td>
                        {if $can_send && $has_active_newsletter_accounts}
                            <td>
                                <a href="{uri args="acp/newsletter/index/send/id_`$row.id`"}" title="{lang t="newsletter|send"}" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                                    <i class="glyphicon glyphicon-envelope" aria-hidden="true"></i>
                                </a>
                            </td>
                        {/if}
                        <td>{$row.id}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
            {if $can_delete === true}
                {include file="asset:system/mark.tpl"}
            {/if}
            {include file="asset:system/datatable.tpl" dt=$datatable_config}
            {javascripts}
                {include_js module="system" file="forms"}
            {/javascripts}
        {else}
            <div class="alert alert-warning text-center">
                <strong>{lang t="system|no_entries"}</strong>
            </div>
        {/if}
    </form>
{/block}