<form action="{uri args="acp/polls/delete"}" method="post">
	<div id="adm-list">
		{check_access mode="link" action="polls|create" uri="acp/polls/create" icon="32/kchart" width="32" height="32"}
		{check_access mode="input" action="polls|delete" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="common|overview"}</h2>
	</div>
	<hr>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($polls)}
{$pagination}
{assign var="can_delete" value=modules::check("polls", "delete")}
	<table class="acp-table">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox"></th>
{/if}
				<th>{lang t="common|publication_period"}</th>
				<th>{lang t="polls|question"}</th>
				<th style="width:3%">{lang t="common|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $polls as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}" class="checkbox"></td>
{/if}
				<td>{$row.period}</td>
				<td>{check_access mode="link" action="polls|edit" uri="acp/polls/edit/id_`$row.id`" title=$row.question}</td>
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