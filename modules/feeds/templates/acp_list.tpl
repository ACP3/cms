{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{uri args="acp/feeds"}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="feed-image" class="control-label">{lang t="feeds|feed_image"}</label>
		<div class="controls"><input type="text" name="feed_image" id="feed-image" value="{$form.feed_image}" maxlength="120"></div>
	</div>
	<div class="control-group">
		<label for="feed-type" class="control-label">{lang t="feeds|feed_type"}</label>
		<div class="controls">
			<select name="feed_type" id="feed-type">
{foreach $feed_types as $row}
				<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
			</select>
		</div>
	</div>
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
		<a href="{uri args="acp/contact"}" class="btn">{lang t="common|cancel"}</a>
		{$form_token}
	</div>
</form>