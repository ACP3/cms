{extends file="asset:gallery/admin/index.create.tpl"}

{block CONTENT append}
    <form action="{uri args="acp/gallery/pictures/delete/id_$gallery_id"}" method="post">
        <nav id="adm-list" class="navbar navbar-default" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex2-collapse">
                    <span class="sr-only">{lang t="system|toggle_navigation"}</span>
                    <span class="icon-bar"></span> <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <span class="navbar-brand">{lang t="gallery|pictures"}</span>
            </div>
            <div class="collapse navbar-collapse navbar-ex2-collapse">
                <div class="navbar-text pull-right">
                    {check_access mode="link" path="acp/gallery/pictures/create/id_`$gallery_id`" class="glyphicon glyphicon-plus text-success"}
                    {if $show_mass_delete_button}
                        {check_access mode="button" path="acp/gallery/pictures/delete" class="glyphicon glyphicon-remove text-danger" lang="system|delete_marked"}
                    {/if}
                </div>
            </div>
        </nav>
        {redirect_message}
        {include file="asset:System/Partials/datagrid.tpl" dataTable=$grid}
    </form>
{/block}

{javascripts}
    {include_js module="system" file="ajax-form"}
{/javascripts}
