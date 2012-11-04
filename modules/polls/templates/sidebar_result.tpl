<ul class="nav nav-list">
	<li class="nav-header">{lang t="polls|latest_poll"}</li>
	<li>
		<h5>{$sidebar_polls.title}</h5>
{foreach $sidebar_poll_answers as $row}
		<strong>{$row.text}</strong><span class="pull-right">{$row.votes}</span>
		<div class="progress active">
			<div class="bar" style="width:{$row.percent}%"></div>
		</div>
{/foreach}
	</li>
	<li><a href="{uri args="polls"}">{lang t="polls|polls_archive"}</a></li>
</ul>