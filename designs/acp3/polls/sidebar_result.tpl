<h4>{lang t="polls|latest_poll"}</h4>
<div id="sidebar-polls">
	<h5>{$sidebar_polls.question}</h5>
	<ul>
{foreach $sidebar_poll_answers as $row}
		<li><strong>{$row.text}:</strong> {$row.percent}% ({$row.votes})</li>
{/foreach}
	</ul>
	<a href="{uri args="polls"}">{lang t="polls|polls_archive"}</a>
</div>