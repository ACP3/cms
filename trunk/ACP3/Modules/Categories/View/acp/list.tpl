<form action="{uri args="acp/categories/delete"}" method="post">
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
                {check_access mode="link" path="acp/categories/create" icon="32/folder_new" width="32" height="32"}
                {check_access mode="link" path="acp/categories/settings" icon="32/advancedsettings" width="32" height="32"}
                {check_access mode="input" path="acp/categories/delete" icon="32/cancel" lang="system|delete_marked"}
            </div>
        </div>
    </nav>
    {if isset($redirect_message)}
        {$redirect_message}
    {/if}
    {if isset($categories)}
        <table id="acp-table" class="table table-striped table-hover">
            <thead>
            <tr>
                {if $can_delete === true}
                    <th style="width:3%"><input type="checkbox" id="mark-all" value="1"></th>
                {/if}
                <th>{lang t="categories|title"}</th>
                <th>{lang t="system|description"}</th>
                <th>{lang t="categories|module"}</th>
                <th style="width:5%">{lang t="system|id"}</th>
            </tr>
            </thead>
            <tbody>
            {foreach $categories as $row}
                <tr>
                    {if $can_delete === true}
                        <td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
                    {/if}
                    <td>{check_access mode="link" path="acp/categories/edit/id_`$row.id`" title=$row.title}</td>
                    <td>{$row.description}</td>
                    <td>{$row.module}</td>
                    <td>{$row.id}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        {if $can_delete === true}
            {mark name="entries"}
        {/if}
    {else}
        <div class="alert alert-warning text-center">
            <h5>{lang t="system|entries"}</h5>
        </div>
    {/if}
</form>