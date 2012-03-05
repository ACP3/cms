<h4>{lang t="polls|latest_poll"}</h4>
{if isset($sidebar_poll_question)}
<div id="sidebar-polls">
	<h5>{$sidebar_poll_question.question}</h5>
	<form action="{uri args="polls/vote/id_`$sidebar_poll_question.id`"}" method="post" accept-charset="UTF-8">
		<ul>
			<li>
{foreach $sidebar_poll_answers as $row}
				<label for="answer-{$row.id}">
{if $sidebar_poll_question.multiple == '1'}
					<input type="checkbox" name="answer[]" id="answer-{$row.id}" value="{$row.id}" class="checkbox">
{else}
					<input type="radio" name="answer" id="answer-{$row.id}" value="{$row.id}" class="checkbox">
{/if}
					{$row.text}
				</label><br>
{/foreach}
			</li>
		</ul>
		<div>
			<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		</div>
	</form>
</div>
{else}
<ul>
	<li>{lang t="common|no_entries_short"}</li>
</ul>
{/if}