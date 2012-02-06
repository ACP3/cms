<form action="{uri args="acp/news/delete"}" method="post">
	<div id="adm-list">
		{check_access mode="link" action="news|create" uri="acp/news/create" icon="32/news" width="32" height="32"}
		{check_access mode="link" action="news|settings" uri="acp/news/settings" icon="32/advancedsettings" width="32" height="32"}
		{check_access mode="input" action="news|delete" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="common|overview"}</h2>
	</div>
	<hr>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($news)}
{$pagination}
{assign var="can_delete" value=modules::check("news", "delete")}
	<table class="acp-table">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox"></th>
{/if}
				<th>{lang t="common|publication_period"}</th>
				<th>{lang t="news|headline"}</th>
				<th>{lang t="common|category"}</th>
				<th style="width:3%">{lang t="common|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $news as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}" class="checkbox"></td>
{/if}
				<td>{$row.period}</td>
				<td>{check_access mode="link" action="news|edit" uri="acp/news/edit/id_`$row.id`" title=$row.headline}</td>
				<td>{$row.cat}</td>
				<td>{$row.id}</td>
			</tr>
{/foreach}
		</tbody>
	</table>
{if $can_delete === true}
{mark name="entries"}
{/if}
{else}
	<div class="error">
		<h5>{lang t="common|no_entries"}</h5>
	</div>
{/if}
</form>