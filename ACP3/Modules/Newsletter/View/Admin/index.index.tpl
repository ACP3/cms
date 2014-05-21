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
                {check_access mode="link" path="acp/newsletter/index/create" icon="32/mail_new" width="32" height="32"}
                {check_access mode="link" path="acp/newsletter/accounts" icon="32/personal" width="32" height="32"}
                {check_access mode="link" path="acp/newsletter/index/settings" icon="32/advancedsettings" width="32" height="32"}
                {check_access mode="input" path="acp/newsletter/index/delete" icon="32/cancel" lang="system|delete_marked"}
            </div>
        </div>
    </nav>
    {if isset($redirect_message)}
        {$redirect_message}
    {/if}
    {if isset($newsletter)}
        <table id="acp-table" class="table table-striped table-hover">
            <thead>
            <tr>
                {if $can_delete === true}
                    <th style="width:3%"><input type="checkbox" id="mark-all" value="1"></th>
                {/if}
                <th style="width:22%">{lang t="system|date"}</th>
                <th>{lang t="newsletter|subject"}</th>
                <th>{lang t="newsletter|status"}</th>
                {if $can_send}
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
                    <td>{$row.date_formatted}</td>
                    <td>{check_access mode="link" path="acp/newsletter/index/edit/id_`$row.id`" title=$row.title}</td>
                    <td>{$row.status}</td>
                    {if $can_send}
                        <td>
                            <a href="{uri args="acp/newsletter/index/send/id_`$row.id`"}" title="{lang t="newsletter|send"}" data-ajax-form="true" data-ajax-form-loading-text="{lang t="system|loading_please_wait"}">
                                {icon path="16/mail_send" width="16" height="16" alt="{lang t="newsletter|send"}"}
                            </a>
                        </td>
                    {/if}
                    <td>{$row.id}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        {if $can_delete === true}
            {mark name="entries"}
        {/if}
        {include_js module="system" file="forms"}
    {else}
        <div class="alert alert-warning text-center">
            <strong>{lang t="system|no_entries"}</strong>
        </div>
    {/if}
</form>