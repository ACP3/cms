{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|publication_period"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="polls|poll"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				{$publication_period}
			</div>
			<div id="tab-2" class="tab-pane">
				<div class="control-group">
					<label for="question" class="control-label">{lang t="polls|question"}</label>
					<div class="controls"><input type="text" name="question" id="question" value="{$question}" maxlength="120"></div>
				</div>
{foreach $answers as $row}
				<div class="control-group">
					<label for="answer-{$row.number}" class="control-label">{lang t="polls|answer"} {$row.number+1}</label>
					<div class="controls">
						<input type="text" name="answers[{$row.number}][value]" id="answer-{$row.number}" value="{$row.value}" maxlength="120">
						<input type="checkbox" name="answers[{$row.number}][delete]" value="1" class="checkbox inline">
						<input type="hidden" name="answers[{$row.number}][id]" value="{$row.id}" style="display:none">
					</div>
				</div>
{/foreach}
				<div class="control-group">
					<label for="{$options.0.name}" class="control-label">{lang t="system|options"}</label>
					<div class="controls">
{foreach $options as $row}
						<label for="{$row.name}" class="checkbox">
							<input type="checkbox" name="{$row.name}" id="{$row.name}" value="1"{$row.checked}>
							{$row.lang}
						</label>
{/foreach}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="form-actions">
{if !$disable}
		<button type="submit" name="add_answer" class="btn">{lang t="polls|add_answer"}</button>
{/if}
		<button type="submit" name="submit" class="btn">{lang t="system|submit"}</button>
		<a href="{uri args="acp/polls"}" class="btn">{lang t="system|cancel"}</a>
		{$form_token}
	</div>
</form>