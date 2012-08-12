<div class="alert alert-block">
	<p>
		{lang t="acp|description"}
	</p>
	<h4>{lang t="acp|access_to_modules"}</h4>
	<ul>
{foreach $modules as $module}
		<li><a href="{uri args="acp/`$module.dir`"}">{$module.name}</a></li>
{/foreach}
	</ul>
</div>