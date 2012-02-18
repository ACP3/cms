{if isset($files)}
{foreach $files as $file}
<div class="files">
	<div class="date">
		{$file.date}
	</div>
	<div class="header">
		<a href="{uri args="files/details/id_`$file.id`" alias="1"}">{$file.link_title} ({$file.size})</a>
	</div>
</div>
{/foreach}
{else}
<div class="error-box">
	<h5>{lang t="common|no_entries"}</h5>
</div>
{/if}