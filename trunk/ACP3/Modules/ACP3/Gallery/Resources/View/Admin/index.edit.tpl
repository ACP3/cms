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
                    {if isset($pictures)}
                        {check_access mode="button" path="acp/gallery/pictures/delete" class="glyphicon glyphicon-remove text-danger" lang="system|delete_marked"}
                    {/if}
                </div>
            </div>
        </nav>
        {redirect_message}
        {if isset($pictures)}
            <table id="acp-table" class="table table-striped table-hover">
                <thead>
                <tr>
                    {if $can_delete === true}
                        <th style="width:3%"><input type="checkbox" id="mark-all" value="1" {mark name="entries"}></th>
                    {/if}
                    <th>{lang t="gallery|picture"}</th>
                    <th>{lang t="system|description"}</th>
                    {if $can_order === true}
                        <th>{lang t="system|order"}</th>
                    {/if}
                    <th style="width:5%">{lang t="system|id"}</th>
                </tr>
                </thead>
                <tbody>
                {foreach $pictures as $row}
                    <tr>
                        {if $can_delete === true}
                            <td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
                        {/if}
                        <td>
                            {if $can_edit_picture}
                                <a href="{uri args="acp/gallery/pictures/edit/id_`$row.id`"}" title="{lang t="gallery|admin_pictures_edit"}">
                                    <img src="{uri args="gallery/index/image/id_`$row.id`/action_thumb"}" alt="">
                                </a>
                            {else}
                                <img src="{uri args="gallery/index/image/id_`$row.id`/action_thumb"}" alt="">
                            {/if}
                        </td>
                        <td>{$row.description}</td>
                        {if $can_order === true}
                            <td>
                                {if !$row.last}
                                    <a href="{uri args="acp/gallery/pictures/order/id_`$row.id`/action_down"}"
                                       title="{lang t="system|move_down"}"
                                       data-ajax-form="true"
                                       data-ajax-form-loading-text="{lang t="system|loading_please_wait"}"><i class="glyphicon glyphicon-arrow-down" aria-hidden="true"></i></a>
                                {/if}
                                {if !$row.first}
                                    <a href="{uri args="acp/gallery/pictures/order/id_`$row.id`/action_up"}"
                                       title="{lang t="system|move_up"}"
                                       data-ajax-form="true"
                                       data-ajax-form-loading-text="{lang t="system|loading_please_wait"}"><i class="glyphicon glyphicon-arrow-up" aria-hidden="true"></i></a>
                                    </a>
                                {/if}
                                {if $row.first && $row.last}
                                    <i class="glyphicon glyphicon-remove-circle text-danger text-danger" aria-hidden="true" title="{lang t="system|move_impossible"}"></i>
                                {/if}
                            </td>
                        {/if}
                        <td>{$row.id}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
            {if $can_delete === true}
                {include file="asset:system/mark.tpl"}
            {/if}
        {else}
            <div class="alert alert-warning text-center">
                <strong>{lang t="system|no_entries"}</strong>
            </div>
        {/if}
    </form>
{/block}

{javascripts}
    {include_js module="system" file="forms"}
{/javascripts}