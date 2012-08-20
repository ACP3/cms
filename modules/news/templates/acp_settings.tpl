{if isset($error_msg)}
{$error_msg}
{/if}
<script type="text/javascript">
$(document).ready(function() {
	$('input[name="readmore"]').bind('click', function() {
		if ($(this).val() == 1) {
			$('#readmore-container').show();
		} else {
			$('#readmore-container').hide();
		}
	});

	$('input[name="readmore"]:checked').trigger('click');
});
</script>
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="control-group">
		<label for="date-format" class="control-label">{lang t="common|date_format"}</label>
		<div class="controls">
			<select name="dateformat" id="date-format">
				<option value="">{lang t="common|pls_select"}</option>
{foreach $dateformat as $row}
				<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
			</select>
		</div>
	</div>
	<div class="control-group">
		<label for="sidebar-entries" class="control-label">{lang t="common|sidebar_entries_to_display"}</label>
		<div class="controls">
			<select name="sidebar" id="sidebar-entries">
				<option>{lang t="common|pls_select"}</option>
{foreach $sidebar_entries as $row}
				<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
			</select>
		</div>
	</div>
	<div class="control-group">
		<label for="readmore-1" class="control-label">{lang t="news|activate_readmore"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="radio">
{foreach $readmore as $row}
				<input type="radio" name="readmore" id="readmore-{$row.value}" value="{$row.value}"{$row.checked}>
				<label for="readmore-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
			</div>
		</div>
	</div>
	<div id="readmore-container" class="control-group">
		<label for="readmore-chars" class="control-label">{lang t="news|readmore_chars"}</label>
		<div class="controls"><input type="number" name="readmore_chars" id="readmore-chars" value="{$readmore_chars}"></div>
	</div>
	<div class="control-group">
		<label for="category-in-breadcrumb-1" class="control-label">{lang t="news|display_category_in_breadcrumb"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="radio">
{foreach $category_in_breadcrumb as $row}
				<input type="radio" name="category_in_breadcrumb" id="category-in-breadcrumb-{$row.value}" value="{$row.value}"{$row.checked}>
				<label for="category-in-breadcrumb-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
			</div>
		</div>
	</div>
{if isset($allow_comments)}
	<div class="control-group">
		<label for="comments-1" class="control-label">{lang t="common|allow_comments"}</label>
		<div class="controls">
			<div class="btn-group" data-toggle="radio">
{foreach $allow_comments as $row}
				<input type="radio" name="comments" id="comments-{$row.value}" value="{$row.value}"{$row.checked}>
				<label for="comments-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
			</div>
		</div>
	</div>
{/if}
	<div class="form-actions">
		<button type="submit" name="submit" class="btn">{lang t="common|submit"}</button>
		<a href="{uri args="acp/news"}" class="btn">{lang t="common|cancel"}</a>
		{$form_token}
	</div>
</form>