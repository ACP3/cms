<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="answer-{$answers.0.id}" class="control-label">{$question}</label>
		<div class="controls">
{foreach $answers as $row}
{if $multiple == '1'}
		<label for="answer-{$row.id}" class="checkbox">
			<input type="checkbox" name="answer[]" id="answer-{$row.id}" value="{$row.id}">
{else}
		<label for="answer-{$row.id}" class="radio">
			<input type="radio" name="answer" id="answer-{$row.id}" value="{$row.id}">
{/if}
			{$row.text}
		</label>
{/foreach}
		</div>
	</div>
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
	</div>
</form>