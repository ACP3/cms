{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="common|publication_period"}</a></li>
			<li><a href="#tab-2">{lang t="polls|poll"}</a></li>
		</ul>
		<div id="tab-1">
			<dl>
				<dt><label for="start">{lang t="common|publication_period"}</label></dt>
				<dd>{$publication_period}</dd>
			</dl>
			<p>
				{lang t="common|date_description"}
			</p>
		</div>
		<div id="tab-2" class="ui-tabs-hide">
			<dl>
				<dt><label for="question">{lang t="polls|question"}</label></dt>
				<dd><input type="text" name="form[question]" id="question" value="{$question}" maxlength="120"></dd>
			</dl>
			<p>
				{lang t="polls|delete_answer_description"}
			</p>
			<table class="acp-table no-border">
{foreach $answers as $row}
				<tr>
					<td style="width:5%;padding:3px 0"><input type="checkbox" name="form[answers][{$row.number}][delete]" value="1" class="checkbox"></td>
					<td style="padding:3px 0">
						<dl>
							<dt><label for="answer_{$row.number}">{lang t="polls|answer"} {$row.number+1}</label></dt>
							<dd>
								<input type="text" name="form[answers][{$row.number}][value]" id="answer_{$row.number}" value="{$row.value}" maxlength="120">
								<input type="hidden" name="form[answers][{$row.number}][id]" value="{$row.id}" style="display:none">
							</dd>
						</dl>
					</td>
				</tr>
{/foreach}
			</table>
			<dl>
				<dt><label for="reset">{lang t="common|options"}</label></dt>
				<dd>
					<ul style="margin:0 20px;list-style:none">
{foreach $options as $row}
						<li>
							<label for="{$row.name}">
								<input type="checkbox" name="form[{$row.name}]" id="{$row.name}" value="1" class="checkbox"{$row.checked}>
								{$row.lang}
							</label>
						</li>
{/foreach}
					</ul>
				</dd>
			</dl>
		</div>
	</div>
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
{if !$disable}
		<input type="submit" name="add_answer" value="{lang t="polls|add_answer"}" class="form">
{/if}
		{$form_token}
	</div>
</form>