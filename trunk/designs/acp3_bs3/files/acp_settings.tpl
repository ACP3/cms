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
	<div class="form-group">
		<label for="sidebar-entries" class="col-lg-2 control-label">{lang t="system|sidebar_entries_to_display"}</label>
		<div class="col-lg-10">
			<select class="form-control" name="sidebar" id="sidebar-entries">
				{foreach $sidebar_entries as $row}
					<option value="{$row.value}"{$row.selected}>{$row.value}</option>
				{/foreach}
			</select>
		</div>
	</div>
	{if isset($comments)}
		<div class="form-group">
			<label for="{$comments.0.id}" class="col-lg-2 control-label">{lang t="system|allow_comments"}</label>
			<div class="col-lg-10">
				<div class="btn-group" data-toggle="buttons">
					{foreach $comments as $row}
						<label for="{$row.id}" class="btn btn-default">
							<input type="radio" name="comments" id="{$row.id}" value="{$row.value}"{$row.checked}>
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
			<a href="{uri args="acp/files"}" class="btn btn-default">{lang t="system|cancel"}</a>
			{$form_token}
		</div>
	</div>
</form>