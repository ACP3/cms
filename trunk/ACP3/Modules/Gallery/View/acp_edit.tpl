{if isset($error_msg)}
    {$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general_statements"}</a></li>
            <li><a href="#tab-2" data-toggle="tab">{lang t="system|seo"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane active">
                {$publication_period}
                <div class="form-group">
                    <label for="title" class="col-lg-2 control-label">{lang t="gallery|title"}</label>

                    <div class="col-lg-10">
                        <input class="form-control" type="text" name="title" id="title" value="{$form.title}" maxlength="120" required>
                    </div>
                </div>
            </div>
            <div id="tab-2" class="tab-pane">
                {$SEO_FORM_FIELDS}
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            <a href="{uri args="acp/gallery"}" class="btn btn-default">{lang t="system|cancel"}</a>
            {$form_token}
        </div>
    </div>
</form>
<form action="{uri args="acp/gallery/delete_picture/id_$gallery_id"}" method="post">
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
                {check_access mode="link" path="acp/gallery/create_picture/id_`$gallery_id`" icon="32/image" width="32" height="32"}
                {check_access mode="input" path="acp/gallery/delete_picture" icon="32/cancel" lang="system|delete_marked"}
            </div>
        </div>
    </nav>
    {if isset($redirect_message)}
        {$redirect_message}
    {/if}
    {if isset($pictures)}
        <table id="acp-table" class="table table-striped table-hover">
            <thead>
            <tr>
                {if $can_delete === true}
                    <th style="width:3%"><input type="checkbox" id="mark-all" value="1"></th>
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
                    <td>{if $can_edit_picture}
                        <a href="{uri args="acp/gallery/edit_picture/id_`$row.id`"}" title="{lang t="gallery|acp_edit_picture"}">
                            <img src="{uri args="gallery/image/id_`$row.id`/action_thumb"}" alt=""></a>{else}
                            <img src="{uri args="gallery/image/id_`$row.id`/action_thumb"}" alt="">{/if}</td>
                    <td>{$row.description}</td>
                    {if $can_order === true}
                        <td>
                            {if !$row.last}
                                <a href="{uri args="acp/gallery/order/id_`$row.id`/action_down"}" title="{lang t="system|move_down"}">{icon path="16/down" width="16" height="16" alt={lang t="system|move_down"}}</a>
                            {/if}
                            {if !$row.first}
                                <a href="{uri args="acp/gallery/order/id_`$row.id`/action_up"}" title="{lang t="system|move_up"}">{icon path="16/up" width="16" height="16" alt={lang t="system|move_up"}}</a>
                            {/if}
                            {if $row.first && $row.last}
                                {icon path="16/editdelete" width="16" height="16" alt={lang t="system|move_impossible"} title={lang t="system|move_impossible"}}
                            {/if}
                        </td>
                    {/if}
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
            <strong>{lang t="system|no_entries"}</strong>
        </div>
    {/if}
</form>