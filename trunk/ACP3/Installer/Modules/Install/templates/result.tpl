{if isset($install_error)}
<p>
	{lang t="installation_error"}
</p>
<div class="well well-sm text-center">
	<a href="{uri args="overview/welcome"}" class="btn btn-default">{lang t="back"}</a>
</div>
{else}
<p>
	{lang t="installation_successful_1"}
</p>
<div class="alert alert-warning">
	{lang t="installation_successful_2"}
</div>
<div class="well well-sm text-center">
	<a href="{$ROOT_DIR}" class="btn btn-default">{lang t="go_to_website"}</a>
	<a href="{$ROOT_DIR}acp/" class="btn btn-default">{lang t="log_into_admin_panel"}</a>
</div>
{/if}