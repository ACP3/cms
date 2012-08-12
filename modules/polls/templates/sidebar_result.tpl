<ul class="nav nav-list">
	<li class="nav-header">{lang t="polls|latest_poll"}</li>
	<li>
		<strong>{$sidebar_polls.question}</strong>
		<ul class="unstyled">
{foreach $sidebar_poll_answers as $row}
			<li><strong>{$row.text}:</strong> {$row.percent}% ({$row.votes})</li>
{/foreach}
		</ul>
	</li>
	<li class="divider"></li>
	<li><a href="{uri args="polls"}">{lang t="polls|polls_archive"}</a></li>
</ul>