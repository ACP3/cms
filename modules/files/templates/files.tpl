{if isset($files)}
{foreach $files as $file}
<div class="dataset-box">
	<div class="header">
		<div class="pull-right small">{$file.date}</div>
		<a href="{uri args="files/details/id_`$file.id`"}">{$file.link_title} ({$file.size})</a>
	</div>
</div>
{/foreach}
{else}
<div class="alert align-center">
	<strong>{lang t="common|no_entries"}</strong>
</div>
{/if}