{if isset($galleries)}
{$pagination}
{foreach $galleries as $row}
<div class="dataset-box">
	<div class="header">
		<div class="small f-right">{$row.date}</div>
		<a href="{uri args="gallery/pics/id_`$row.id`"}">{$row.name} ({$row.pics})</a>
	</div>
</div>
{/foreach}
{else}
<div class="alert alert-block align-center">
	<h5>{lang t="common|no_entries"}</h5>
</div>
{/if}