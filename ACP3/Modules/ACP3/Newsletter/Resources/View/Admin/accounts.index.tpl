{extends file="asset:`$LAYOUT`"}

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
            {if $show_mass_delete_button}
                <div class="collapse navbar-collapse navbar-ex2-collapse">
                    <div class="navbar-text pull-right">
                        {check_access mode="button" path="acp/newsletter/accounts/delete" class="glyphicon glyphicon-remove text-danger" lang="system|delete_marked"}
                    </div>
                </div>
            {/if}
        </nav>
        {redirect_message}
        {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
    </form>
{/block}
