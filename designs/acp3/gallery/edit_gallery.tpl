{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="common|general_statements"}</a></li>
			<li><a href="#tab-2">{lang t="common|seo"}</a></li>
		</ul>
		<div id="tab-1">
			{$publication_period}
			<dl>
				<dt><label for="name">{lang t="gallery|title"}</label></dt>
				<dd><input type="text" name="name" id="name" value="{$form.name}" maxlength="120"></dd>
			</dl>
		</div>
		<div id="tab-2">
			{$SEO_FORM_FIELDS}
		</div>
	</div>
	<div class="form-bottom" style="margin:20px 0">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>
<form action="{uri args="acp/gallery/delete_picture/id_$gallery_id"}" method="post">
	<div id="adm-list">
		{check_access mode="link" action="gallery|add_picture" uri="acp/gallery/add_picture/id_`$gallery_id`" icon="32/image" width="32" height="32"}
		{check_access mode="input" action="gallery|delete_picture" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="gallery|pictures"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($pictures)}
{$pagination}
{assign var="can_delete" value=ACP3_Modules::check("gallery", "delete_picture")}
{assign var="can_order" value=ACP3_Modules::check("gallery", "order")}
{assign var="can_edit_picture" value=ACP3_Modules::check("gallery", "edit_picture")}
	<table class="acp-table">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox"></th>
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
				<td><input type="checkbox" name="entries[]" value="{$row.id}" class="checkbox"></td>
{/if}
				<td>{if $can_edit_picture}<a href="{uri args="acp/gallery/edit_picture/id_`$row.id`"}" title="{lang t="gallery|edit_picture"}"><img src="{uri args="gallery/image/id_`$row.id`/action_thumb"}" alt=""></a>{else}<img src="{uri args="gallery/image/id_`$row.id`/action_thumb"}" alt="">{/if}</td>
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
	<div class="error-box">
		<h5>{lang t="common|no_entries"}</h5>
	</div>
{/if}
</form>