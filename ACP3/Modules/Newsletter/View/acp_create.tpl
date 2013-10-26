{if isset($error_msg)}
	{$error_msg}
{/if}
{include_js module="newsletter" file="acp"}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="form-group">
		<label for="title" class="col-lg-2 control-label">{lang t="newsletter|subject"}</label>
		<div class="col-lg-10"><input class="form-control" type="text" name="title" id="title" value="{$form.title}" required></div>
	</div>
	<div class="form-group">
		<label for="text" class="col-lg-2 control-label">{lang t="newsletter|text"}</label>
		<div class="col-lg-10"><textarea class="form-control" name="text" id="text" cols="50" rows="5" required>{$form.text}</textarea></div>
	</div>
	<div class="form-group">
		<label for="action-1" class="col-lg-2 control-label">{lang t="newsletter|action"}</label>
		<div class="col-lg-10">
			<div class="btn-group" data-toggle="buttons">
				{foreach $action as $row}
					<label for="action-{$row.value}" class="btn btn-default{if !empty($row.checked)} active{/if}">
						<input type="radio" name="action" id="action-{$row.value}" value="{$row.value}"{$row.checked}>
						{$row.lang}
					</label>
				{/foreach}
			</div>
		</div>
	</div>
	<div id="test-newsletter" class="form-group">
		<label for="test-1" class="col-lg-2 control-label">{lang t="newsletter|test_newsletter"}</label>
		<div class="col-lg-10">
			<div class="btn-group" data-toggle="buttons">
				{foreach $test as $row}
					<label for="test-{$row.value}" class="btn btn-default{if !empty($row.checked)} active{/if}">
						<input type="radio" name="test" id="test-{$row.value}" value="{$row.value}"{$row.checked}>
						{$row.lang}
					</label>
				{/foreach}
			</div>
			<p class="help-block">{lang t="newsletter|test_nl_description"}</p>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-offset-2 col-lg-10">
			<button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
			<a href="{uri args="acp/newsletter"}" class="btn btn-default">{lang t="system|cancel"}</a>
			{$form_token}
		</div>
	</div>
</form>