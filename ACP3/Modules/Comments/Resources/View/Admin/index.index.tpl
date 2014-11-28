{extends file="asset:layout.tpl"}

{block CONTENT}
    <form action="{uri args="acp/comments/index/delete"}" method="post">
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
                    {check_access mode="link" path="acp/comments/index/settings" class="glyphicon glyphicon-wrench"}
                    {check_access mode="button" path="acp/comments/index/delete" class="glyphicon glyphicon-remove text-danger" lang="system|delete_marked"}
                </div>
            </div>
        </nav>
        {redirect_message}
        {if isset($comments)}
            <table id="acp-table" class="table table-striped table-hover">
                <thead>
                <tr>
                    {if $can_delete === true}
                        <th style="width:3%"><input type="checkbox" id="mark-all" value="1" {mark name="entries"}></th>
                    {/if}
                    <th>{lang t="comments|module"}</th>
                    <th>{lang t="comments|comments_count"}</th>
                </tr>
                </thead>
                <tbody>
                {foreach $comments as $row}
                    <tr>
                        {if $can_delete === true}
                            <td><input type="checkbox" name="entries[]" value="{$row.module_id}"></td>
                        {/if}
                        <td>{check_access mode="link" path="acp/comments/details/index/id_`$row.module_id`" lang="comments|show_comments" title=$row.name}</td>
                        <td>{$row.comments_count}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {else}
            <div class="alert alert-warning text-center">
                <strong>{lang t="system|no_entries"}</strong>
            </div>
        {/if}
    </form>
    {javascripts}
    {if isset($comments)}
        {if $can_delete === true}
            {include file="asset:system/mark.tpl"}
        {/if}
        {include file="asset:system/datatable.tpl" dt=$datatable_config}
    {/if}
    {/javascripts}
{/block}