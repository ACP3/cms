{if isset($install_error)}
<p>
	{lang t="installation|installation_error"}
</p>
<div class="form-actions" style="text-align:center">
	<a href="{uri args="overview/welcome"}" class="btn">{lang t="common|back"}</a>
</div>
{else}
<p>
	{lang t="installation|installation_successful_1"}
</p>
<p>
	{lang t="installation|installation_successful_2"}
</p>
<div class="form-actions" style="text-align:center">
	<a href="{$ROOT_DIR}" class="btn">{lang t="common|forward"}</a>
</div>
{/if}