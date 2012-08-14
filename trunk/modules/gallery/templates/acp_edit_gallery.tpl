{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="common|general_statements"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="common|seo"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				{$publication_period}
				<div class="control-group">
					<label for="name" class="control-label">{lang t="gallery|title"}</label>
					<div class="controls"><input type="text" name="name" id="name" value="{$form.name}" maxlength="120"></div>
				</div>
			</div>
			<div id="tab-2" class="tab-pane">
				{$SEO_FORM_FIELDS}
			</div>
		</div>
	</div>
	<div class="form-actions" style="margin:20px 0">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		{$form_token}
	</div>
</form>
<form action="{uri args="acp/gallery/delete_picture/id_$gallery_id"}" method="post">
	<div id="adm-list" class="well">
		{check_access mode="link" path="acp/gallery/add_picture/id_`$gallery_id`" icon="32/image" width="32" height="32"}
		{check_access mode="input" path="acp/gallery/delete_picture" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="gallery|pictures"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($pictures)}
{$pagination}
	<table class="table table-striped">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox inline"></th>
{/if}
				<th>{lang t="gallery|picture"}</th>
				<th>{lang t="common|description"}</th>
{if $can_order === true}
				<th>{lang t="common|order"}</th>
{/if}
				<th style="width:3%">{lang t="common|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $pictures as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}" class="checkbox inline"></td>
{/if}
				<td>{if $can_edit_picture}<a href="{uri args="acp/gallery/edit_picture/id_`$row.id`"}" title="{lang t="gallery|acp_edit_picture"}"><img src="{uri args="gallery/image/id_`$row.id`/action_thumb"}" alt=""></a>{else}<img src="{uri args="gallery/image/id_`$row.id`/action_thumb"}" alt="">{/if}</td>
				<td>{$row.description}</td>
{if $can_order === true}
				<td>
{if !$row.last}
					<a href="{uri args="acp/gallery/order/id_`$row.id`/action_down"}" title="{lang t="common|move_down"}">{icon path="16/down" width="16" height="16" alt="{lang t="common|move_down"}"}</a>
{/if}
{if !$row.first}
					<a href="{uri args="acp/gallery/order/id_`$row.id`/action_up"}" title="{lang t="common|move_up"}">{icon path="16/up" width="16" height="16" alt="{lang t="common|move_up"}"}</a>
{/if}
{if $row.first && $row.last}
					{icon path="16/editdelete" width="16" height="16" alt="{lang t="common|move_impossible"}" title="{lang t="common|move_impossible"}"}
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
	<div class="alert alert-block align-center">
		<h5>{lang t="common|no_entries"}</h5>
	</div>
{/if}
</form>