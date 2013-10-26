<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">{lang t="files|latest_files"}</h3>
	</div>
	<div class="panel-body">
		<div class="list-group" style="margin-bottom: 0">
			{if isset($sidebar_files)}
				{foreach $sidebar_files as $row}
					<a href="{uri args="files/details/id_`$row.id`"}" class="list-group-item" title="{$row.start} - {$row.title}">{$row.title_short}</a>
				{/foreach}
			{else}
				<span class="list-group-item">{lang t="system|no_entries_short"}</span>
			{/if}
		</div>
	</div>
</div>