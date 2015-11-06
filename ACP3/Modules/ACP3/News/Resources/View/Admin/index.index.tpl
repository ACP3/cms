{extends file="asset:layout.tpl"}

{block CONTENT}
    <form action="{uri args="acp/news/index/delete"}" method="post">
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
                    {check_access mode="link" path="acp/news/index/create" class="glyphicon glyphicon-plus text-success"}
                    {check_access mode="link" path="acp/news/index/settings" class="glyphicon glyphicon-cog"}
                    {if $show_mass_delete_button}
                        {check_access mode="button" path="acp/news/index/delete" class="glyphicon glyphicon-remove text-danger" lang="system|delete_marked"}
                    {/if}
                </div>
            </div>
        </nav>
        {redirect_message}
        {$grid}
    </form>
{/block}