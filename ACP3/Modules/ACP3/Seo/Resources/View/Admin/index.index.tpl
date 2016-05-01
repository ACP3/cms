{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    <form action="{uri args="acp/seo/index/delete"}" method="post">
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
                    {check_access mode="link" path="acp/seo/index/create" class="glyphicon glyphicon-plus text-success"}
                    {check_access mode="link" path="acp/seo/index/settings" class="glyphicon glyphicon-cog"}
                    {if $show_mass_delete_button}
                        {check_access mode="button" path="acp/seo/index/delete" class="glyphicon glyphicon-remove text-danger" lang="system|delete_marked"}
                    {/if}
                </div>
            </div>
        </nav>
        {redirect_message}
        {include file="asset:system/datagrid.tpl" dataTable=$grid}
    </form>
{/block}
