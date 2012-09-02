{if isset($install_error)}
<p>
	{lang t="installation_error"}
</p>
<div class="form-actions" style="text-align:center">
	<a href="{uri args="overview/welcome"}" class="btn">{lang t="back"}</a>
</div>
{else}
<p>
	{lang t="installation_successful_1"}
</p>
<div class="alert">
	{lang t="installation_successful_2"}
</div>
<div class="form-actions" style="text-align:center">
	<a href="{$ROOT_DIR}" class="btn btn-primary">{lang t="go_to_website"}</a>
	<a href="{$ROOT_DIR}" class="btn">{lang t="log_into_admin_panel"}</a>
</div>
{/if}