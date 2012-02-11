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
{foreach $answers as $row}
				<dt><label for="answer_{$row.number}">{lang t="polls|answer"} {$row.number+1}</label></dt>
				<dd><input type="text" name="form[answers][]" id="answer_{$row.number}" value="{$row.value}" maxlength="120"></dd>
{/foreach}
				<dt><label for="multiple">{lang t="common|options"}</label></dt>
				<dd style="margin:0 20px">
					<label for="multiple">
						<input type="checkbox" name="form[multiple]" id="multiple" value="1" class="checkbox"{$multiple}>
						{lang t="polls|multiple_choice"}
					</label>
				</dd>
			</dl>
		</div>
	</div>
	<div class="form-bottom">
{if !$disable}
		<input type="submit" name="add_answer" value="{lang t="polls|add_answer"}" class="form">
{/if}
		<input type="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>