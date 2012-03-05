<form action="{uri args="acp/emoticons/delete"}" method="post">
	<div id="adm-list">
		{check_access mode="link" action="emoticons|create" uri="acp/emoticons/create" icon="32/ksmiletris" width="32" height="32"}
		{check_access mode="link" action="emoticons|settings" uri="acp/emoticons/settings" icon="32/advancedsettings" width="32" height="32"}
		{check_access mode="input" action="emoticons|delete" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="common|overview"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($emoticons)}
{$pagination}
{assign var="can_delete" value=ACP3_Modules::check("emoticons", "delete")}
	<table class="acp-table">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox"></th>
{/if}
				<th>{lang t="common|description"}</th>
				<th>{lang t="emoticons|code"}</th>
				<th>{lang t="emoticons|picture"}</th>
				<th style="width:3%">{lang t="common|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $emoticons as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}" class="checkbox"></td>
{/if}
				<td>{check_access mode="link" action="emoticons|edit" uri="acp/emoticons/edit/id_`$row.id`" title=$row.description}</td>
				<td>{$row.code}</td>
				<td><img src="{$ROOT_DIR}uploads/emoticons/{$row.img}" alt=""></td>
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