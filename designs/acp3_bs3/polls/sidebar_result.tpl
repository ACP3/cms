<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">{lang t="polls|latest_poll"}</h3>
	</div>
	<div class="panel-body">
		<h5>{$sidebar_polls.title}</h5>
		{foreach $sidebar_poll_answers as $row}
			<strong>{$row.text}</strong><span class="pull-right">{$row.votes}</span>
			<div class="progress active">
				<div class="progress-bar" role="progressbar" aria-valuenow="{$row.percent}" aria-valuemin="0" aria-valuemax="100" style="width:{$row.percent}%"></div>
			</div>
		{/foreach}
		<div class="list-group" style="margin-bottom: 0">
			<a href="{uri args="polls"}" class="list-group-item">{lang t="polls|polls_archive"}</a>
		</div>
	</div>
</div>