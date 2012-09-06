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
					<label for="answer_{$row.number}" class="control-label">{lang t="polls|answer"} {$row.number+1}</label>
					<div class="controls"><input type="text" name="answers[]" id="answer_{$row.number}" value="{$row.value}" maxlength="120"></div>
				</div>
{/foreach}
				<div class="control-group">
					<label for="multiple" class="control-label">{lang t="system|options"}</label>
					<div class="controls">
						<label for="multiple" class="checkbox inline">
							<input type="checkbox" name="multiple" id="multiple" value="1"{$multiple}>
							{lang t="polls|multiple_choice"}
						</label>
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