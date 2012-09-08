{if isset($results)}
<ul>
{foreach $results as $row}
	<li>
		{$row.text}
		<span class="label label-{$row.class}">{$row.result_text}</span>
	</li>
{/foreach}
</ul>
{if isset($legacy)}
<form action="{uri args="install/db_update"}" method="post">
	<div class="form-actions" style="text-align:center">
		<button type="submit" name="update" class="btn">{lang t="forward"}</button>
	</div>
</form>
{else}
<p>
	{lang t="db_update_next_steps"}
</p>
<div class="alert">
	{lang t="installation_successful_2"}
</div>
<div class="form-actions" style="text-align:center">
	<a href="{$ROOT_DIR}" class="btn btn-primary">{lang t="go_to_website"}</a>
</div>
{/if}
{else}
{if isset($legacy)}
<p>{lang t="legacy_db_update_description"}</p>
{else}
<p>{lang t="db_update_description"}</p>
{/if}
<form action="{$REQUEST_URI}" method="post">
	<div class="form-actions" style="text-align:center">
		<button type="submit" name="update" class="btn">{lang t="do_db_update"}</button>
	</div>
</form>
{/if}