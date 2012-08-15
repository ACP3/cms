{if isset($files)}
{foreach $files as $file}
<div class="dataset-box">
	<div class="header">
		<div class="f-right small">{$file.date}</div>
		<a href="{uri args="files/details/id_`$file.id`"}">{$file.link_title} ({$file.size})</a>
	</div>
</div>
{/foreach}
{else}
<div class="alert alert-block align-center">
	<h5>{lang t="common|no_entries"}</h5>
</div>
{/if}