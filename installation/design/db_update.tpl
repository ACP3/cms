{if isset($results)}
<div class="alert alert-warning">
	<ul>
{foreach $results as $row}
		<li>
			<strong>{$row.text}</strong>
			<span class="label label-{$row.class}">{$row.result_text}</span>
		</li>
{/foreach}
	</ul>
</div>
{else}
<p>{lang t="installation|db_update_description"}</p>
<div class="form-actions" style="text-align:center">
	<a href="{uri args="install/db_update/action_do"}" class="btn">{lang t="installation|do_db_update"}</a>
</div>
{/if}