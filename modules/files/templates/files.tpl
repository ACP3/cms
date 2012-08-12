{if isset($files)}
{foreach $files as $file}
<div class="files">
	<div class="date">
		{$file.date}
	</div>
	<div class="header">
		<a href="{uri args="files/details/id_`$file.id`"}">{$file.link_title} ({$file.size})</a>
	</div>
</div>
{/foreach}
{else}
<div class="alert alert-block">
	<h5>{lang t="common|no_entries"}</h5>
</div>
{/if}