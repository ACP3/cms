{if isset($error_msg)}
	{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="form-group">
		<label for="date-format" class="col-lg-2 control-label">{lang t="system|date_format"}</label>
		<div class="col-lg-10">
			<select class="form-control" name="dateformat" id="date-format">
				{foreach $dateformat as $row}
					<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
				{/foreach}
			</select>
		</div>
	</div>
	{if isset($allow_emoticons)}
		<div class="form-group">
			<label for="{$allow_emoticons.0.id}" class="col-lg-2 control-label">{lang t="comments|allow_emoticons"}</label>
			<div class="col-lg-10">
				<div class="btn-group" data-toggle="buttons">
					{foreach $allow_emoticons as $row}
						<label for="{$row.id}" class="btn btn-default">
							<input type="radio" name="emoticons" id="{$row.id}" value="{$row.value}"{$row.checked}>
							{$row.lang}
						</label>
					{/foreach}
				</div>
			</div>
		</div>
	{/if}
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
			<a href="{uri args="acp/comments"}" class="btn btn-default">{lang t="system|cancel"}</a>
			{$form_token}
		</div>
	</div>
</form>