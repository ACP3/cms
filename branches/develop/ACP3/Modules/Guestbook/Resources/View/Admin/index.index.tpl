{extends file="asset:layout.tpl"}

{block CONTENT}
    <form action="{uri args="acp/guestbook/index/delete"}" method="post">
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
                    {check_access mode="link" path="acp/guestbook/index/settings" class="glyphicon glyphicon-wrench"}
                    {check_access mode="button" path="acp/guestbook/index/delete" class="glyphicon glyphicon-remove text-danger" lang="system|delete_marked"}
                </div>
            </div>
        </nav>
        {redirect_message}
        {if isset($guestbook)}
            <table id="acp-table" class="table table-striped table-hover">
                <thead>
                <tr>
                    {if $can_delete === true}
                        <th style="width:3%"><input type="checkbox" id="mark-all" value="1" {mark name="entries"}></th>
                    {/if}
                    <th style="width:22%">{lang t="system|date"}</th>
                    <th>{lang t="system|name"}</th>
                    <th>{lang t="system|message"}</th>
                    <th>{lang t="guestbook|ip"}</th>
                    <th style="width:5%">{lang t="system|id"}</th>
                </tr>
                </thead>
                <tbody>
                {foreach $guestbook as $row}
                    <tr>
                        {if $can_delete === true}
                            <td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
                        {/if}
                        <td>{date_format date=$row.date}</td>
                        <td>{check_access mode="link" path="acp/guestbook/index/edit/id_`$row.id`" title=$row.name}</td>
                        <td>{$row.message|nl2p}</td>
                        <td>{$row.ip}</td>
                        <td>{$row.id}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
            {if $can_delete === true}
                {include file="asset:system/mark.tpl"}
            {/if}
            {include file="asset:system/datatable.tpl" dt=$datatable_config}
        {else}
            <div class="alert alert-warning text-center">
                <strong>{lang t="system|no_entries"}</strong>
            </div>
        {/if}
    </form>
{/block}