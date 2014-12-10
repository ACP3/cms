{extends file="asset:layout.tpl"}

{block CONTENT}
    <form action="{uri args="acp/newsletter/accounts/delete"}" method="post">
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
                    {check_access mode="button" path="acp/newsletter/accounts/delete" class="glyphicon glyphicon-remove text-danger" lang="system|delete_marked"}
                </div>
            </div>
        </nav>
        {redirect_message}
        {if isset($accounts)}
            <table id="acp-table" class="table table-striped table-hover">
                <thead>
                <tr>
                    {if $can_delete === true}
                        <th style="width:3%"><input type="checkbox" id="mark-all" value="1" {mark name="entries"}></th>
                    {/if}
                    <th>{lang t="system|email_address"}</th>
                    <th>{lang t="newsletter|status"}</th>
                    <th style="width:5%">{lang t="system|id"}</th>
                </tr>
                </thead>
                <tbody>
                {foreach $accounts as $row}
                    <tr>
                        {if $can_delete === true}
                            <td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
                        {/if}
                        <td>{$row.mail}</td>
                        <td>
                            {if !empty($row.hash)}
                                <a href="{uri args="acp/newsletter/accounts/activate/id_`$row.id`"}" title="{lang t="newsletter|activate_account"}">
                                    <i class="glyphicon glyphicon-remove text-danger"></i>
                                </a>
                            {else}
                                <i class="glyphicon glyphicon-ok text-success"></i>
                            {/if}
                        </td>
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