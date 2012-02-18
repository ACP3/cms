{if isset($polls)}
<table class="acp-table">
	<thead>
		<tr>
			<th>{lang t="polls|question"}</th>
			<th>{lang t="polls|votes"}</th>
			<th>{lang t="common|end_date"}</th>
		</tr>
	</thead>
	<tbody>
{foreach $polls as $row}
		<tr>
			<td><a href="{uri args="polls/`$row.link`/id_`$row.id`"}">{$row.question}</a></td>
			<td>{$row.votes}</td>
			<td>{$row.date}</td>
		</tr>
{/foreach}
	</tbody>
</table>
{else}
<div class="error-box">
	<h5>{lang t="common|no_entries"}</h5>
</div>
{/if}