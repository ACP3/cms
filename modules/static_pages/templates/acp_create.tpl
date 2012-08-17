{if isset($error_msg)}
{$error_msg}
{/if}
<script type="text/javascript" src="{$DESIGN_PATH}js/static_pages_admin.js"></script>
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab-1" data-toggle="tab">{lang t="common|publication_period"}</a></li>
			<li><a href="#tab-2" data-toggle="tab">{lang t="static_pages|page_statements"}</a></li>
			<li><a href="#tab-3" data-toggle="tab">{lang t="common|seo"}</a></li>
		</ul>
		<div class="tab-content">
			<div id="tab-1" class="tab-pane active">
				{$publication_period}
			</div>
			<div id="tab-2" class="tab-pane">
				<div class="control-group">
					<label for="title" class="control-label">{lang t="static_pages|title"}</label>
					<div class="controls"><input type="text" name="title" id="title" value="{$form.title}" maxlength="120"></div>
				</div>
				<div class="control-group">
					<label for="text" class="control-label">{lang t="static_pages|text"}</label>
					<div class="controls">{wysiwyg name="text" value="`$form.text`" height="250" advanced="1"}</div>
				</div>
{if isset($options)}
				<div class="control-group">
					<label for="{$options.0.name}" class="control-label">{lang t="common|options"}</label>
					<div class="controls">
{foreach $options as $row}
						<label for="{$row.name}" class="checkbox">
							<input type="checkbox" name="{$row.name}" id="{$row.name}" value="1"{$row.checked}>
							{$row.lang}
						</label>
{/foreach}
					</div>
				</div>
{/if}
				<div id="create-item-container">
					<div class="control-group">
						<label for="block-id" class="control-label">{lang t="menu_items|blocks"}</label>
						<div class="controls">
							<select name="block_id" id="block-id">
								<option value="">{lang t="common|pls_select"}</option>
{foreach $blocks as $row}
								<option value="{$row.id}"{$row.selected}>{$row.title}</option>
{/foreach}
							</select>
						</div>
					</div>
					<div class="control-group">
						<label for="parent" class="control-label">{lang t="menu_items|superior_page"}</label>
						<div class="controls">
							<select name="parent" id="parent">
								<option value="">{lang t="menu_items|no_superior_page"}</option>
{foreach $pages_list as $block => $pages}
								<optgroup label="{$block}">
{foreach $pages as $row}
									<option value="{$row.id}"{$row.selected}>{$row.spaces}{$row.title}</option>
{/foreach}
								</optgroup>
{/foreach}
							</select>
						</div>
					</div>
					<div class="control-group">
						<label for="display-1" class="control-label">{lang t="menu_items|display_item"}</label>
						<div class="controls">
							<div class="btn-group" data-toggle="radio">
{foreach $display as $row}
								<input type="radio" name="display" id="display-{$row.value}" value="{$row.value}"{$row.selected}>
								<label for="display-{$row.value}" class="btn">{$row.lang}</label>
{/foreach}
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="tab-3" class="tab-pane">
				{$SEO_FORM_FIELDS}
			</div>
		</div>
	</div>
	<div class="form-actions">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
		<input type="reset" value="{lang t="common|reset"}" class="btn">
		{$form_token}
	</div>
</form>