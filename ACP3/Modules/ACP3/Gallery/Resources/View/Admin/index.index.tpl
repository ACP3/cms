{extends file="asset:layout.tpl"}

{block CONTENT}
    <form action="{uri args="acp/gallery/index/delete"}" method="post">
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
                    {check_access mode="link" path="acp/gallery/index/create" class="glyphicon glyphicon-plus text-success"}
                    {check_access mode="link" path="acp/gallery/index/settings" class="glyphicon glyphicon-cog"}
                    {if isset($galleries)}
                        {check_access mode="button" path="acp/gallery/index/delete" class="glyphicon glyphicon-remove text-danger" lang="system|delete_marked"}
                    {/if}
                </div>
            </div>
        </nav>
        {redirect_message}
        {if isset($galleries)}
            <table id="acp-table" class="table table-striped table-hover">
                <thead>
                <tr>
                    {if $can_delete === true}
                        <th style="width:3%"><input type="checkbox" id="mark-all" value="1" {mark name="entries"}></th>
                    {/if}
                    <th style="width:22%">{lang t="system|publication_period"}</th>
                    <th>{lang t="gallery|title"}</th>
                    <th>{lang t="gallery|pictures"}</th>
                    <th style="width:5%">{lang t="system|id"}</th>
                </tr>
                </thead>
                <tbody>
                {foreach $galleries as $row}
                    <tr>
                        {if $can_delete === true}
                            <td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
                        {/if}
                        <td data-order="{date_format date=$row.start format="U"}">{date_range start=$row.start end=$row.end}</td>
                        <td>{check_access mode="link" path="acp/gallery/index/edit/id_`$row.id`" title=$row.title}</td>
                        <td>{$row.pictures}</td>
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