<div class="alert alert-block">
	<strong>{lang t="acp|access_to_modules"}</strong>
	<ul>
{foreach $modules as $module}
		<li><a href="{uri args="acp/`$module.dir`"}">{$module.name}</a></li>
{/foreach}
	</ul>
</div>